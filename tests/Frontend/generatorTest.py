import time
import random
import unittest
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support.ui import Select
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains

class RouteGenerationTest(unittest.TestCase):

    def setUp(self):
        self.driver = webdriver.Chrome()  # Użyj odpowiedniego WebDrivera
        self.driver.get("http://localhost:8000/generator")
        self.wait = WebDriverWait(self.driver, 10)
        

    def test_route_generation(self):
        driver = self.driver

        time.sleep(5)  # Poczekaj na reakcję strony

        def random_select_dropdown(element_id):
            dropdown = Select(driver.find_element(By.ID, element_id))
            options = dropdown.options[1:]  # Pomijamy pierwszy element "Wybierz..."
            random.choice(options).click()

        def random_select_checkbox(group_id):
            scroll_to_bottom_with_element_id_check(group_id)
            if random.choice([True, False]):
                checkboxes = driver.find_elements(By.CSS_SELECTOR, f"#{group_id} input[type='checkbox']")
                random.choice(checkboxes).click()

        def random_select_toggle(element_id):
            scroll_to_bottom_with_element_id_check(element_id)
            if random.choice([True, False]):
                driver.find_element(By.ID, element_id).click()

        def scroll_to_bottom_with_element_id_check(button_id):
            previous_height = driver.execute_script("return document.body.scrollHeight")
            while True:
                # Sprawdź, czy przycisk jest w widoku
                try:
                    button = driver.find_element(By.ID, button_id)
                    # Sprawdź, czy przycisk jest widoczny w oknie przeglądarki
                    is_in_viewport = driver.execute_script(
                        "var elem = arguments[0];"
                        "var bounding = elem.getBoundingClientRect();"
                        "return bounding.top >= 0 && bounding.left >= 0 && "
                        "bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) && "
                        "bounding.right <= (window.innerWidth || document.documentElement.clientWidth);",
                        button
                    )
                    if is_in_viewport:
                        break  # Przycisk jest widoczny, przerywamy pętlę
                except:
                    pass  # Jeśli element nie został jeszcze załadowany, kontynuuj przewijanie

                # Przewiń stronę w dół
                driver.execute_script("window.scrollBy(0, 500);")
                time.sleep(0.5)  # Czekaj na potencjalne doładowanie strony

                # Sprawdź, czy dotarliśmy do końca strony
                new_height = driver.execute_script("return document.body.scrollHeight")
                if new_height == previous_height:
                    break
                previous_height = new_height

        def click_button(button_id):
            try:
                # Przewiń stronę, aby znaleźć przycisk w widoku
                scroll_to_bottom_with_element_id_check(button_id)

                # Poczekaj, aż przycisk stanie się klikalny
                button = self.wait.until(EC.element_to_be_clickable((By.ID, button_id)))

                # Kliknij przycisk
                button.click()
            except:
                # Debuguj problem
                print("Element zasłonięty lub nieklikalny: " + button_id)
                overlays = driver.find_elements(By.CSS_SELECTOR, ".overlay, .modal")
                for overlay in overlays:
                    print(f"Zasłaniający element: {overlay.get_attribute('id')}, {overlay.get_attribute('class')}")

        def select_radio_button(label_for_id):
            try:
                # Znajdź etykietę powiązaną z danym radio buttonem
                label = driver.find_element(By.CSS_SELECTOR, f"label[for='{label_for_id}']")
                label.click()
            except Exception as e:
                print(f"Błąd podczas klikania przycisku radio: {str(e)}")

        # def close_all_modals():
        #     modals = ["mapModal", "tag-modal"]
        #     for modal_id in modals:
        #         driver.execute_script(f"$('#{modal_id}').modal('hide')")
        #         wait_for_modal_to_close(modal_id)
        
        # def wait_for_modal_to_close(modal_id):
        #     self.wait.until(EC.invisibility_of_element((By.ID, modal_id)))


        def verify_file_download_with_criteria(directory, name_contains="mapa", file_extension=".pdf", max_age_seconds=15):

            current_time = time.time()
            
            for file_name in os.listdir(directory):
                if name_contains in file_name and file_name.endswith(file_extension):
                    file_path = os.path.join(directory, file_name)
                    file_age = current_time - os.path.getmtime(file_path)
                    
                    if file_age <= max_age_seconds:
                        print(f"Znaleziono plik: {file_name}, czas od utworzenia pliku: {file_age:.2f} sekundy")
                        return True
            
            print("Nie znaleziono odpowiedniego pliku.")
            return False

        # Funkcja próbująca wygenerować trasę
        def try_generate_route():
            
            time.sleep(2)  # Poczekaj na reakcję strony

            start_time = time.time()
            timeout = 45
            while time.time() - start_time < timeout:
                try:
                    spinner = driver.find_element(By.ID, 'loading-spinner')
                    if spinner.is_displayed():
                        time.sleep(2)
                    else:
                        break
                except Exception:
                    break

            if driver.find_element(By.ID, "alertMessage").is_displayed():
                if "Nie udało się wygenerować trasy" in driver.page_source:
                    if random.choice([True, False]):
                        regenerate_buttons = driver.find_elements(By.CLASS_NAME, "re-generate-btn")
                        if regenerate_buttons:
                            regenerate_buttons[0].click()
                            time.sleep(2)
                            return try_generate_route()
                    else:
                        change_parameters_buttons = driver.find_elements(By.CLASS_NAME, "change-parameters-btn")
                        if change_parameters_buttons:
                            change_parameters_buttons[0].click()
                            return False  # Wymagana zmiana parametrów
                else:
                    return True  # Tabela została wygenerowana

            return True  # Tabela została wygenerowana

        # Generowanie parametrów
        while True:
            time.sleep(2)  # Poczekaj na reakcję strony
            random_select_dropdown("start-point")
            random_select_dropdown("end-point")
            random_select_dropdown("distance")
            random_select_dropdown("points")
            random_select_checkbox("areas-list")
            random_select_toggle("virtualpoints")

            
            click_button("generate-btn")
            success = try_generate_route()
            if success:
                break

        # Kliknięcie na Pobierz mapę w tabeli
        table_rows = driver.find_elements(By.CSS_SELECTOR, "#tbody tr")
        random_row = random.choice(table_rows)
        download_button = random_row.find_element(By.CLASS_NAME, "btn-success")
        download_button.click()

        # Oczekiwanie na modal
        self.wait.until(EC.visibility_of_element_located((By.ID, "mapModal")))

        # Wybór losowego przycisku w modalu i kliknięcie Pobierz
        # radio_buttons = driver.find_elements(By.CSS_SELECTOR, "#mapList input[type='radio']")
        # random.choice(radio_buttons).click()

        # Znajdź wszystkie radio buttony
        radio_buttons = driver.find_elements(By.CSS_SELECTOR, "#mapList input[type='radio']")

        # Wybierz losowy radio button
        random_radio = random.choice(radio_buttons)

        # Pobierz ID wybranego radio buttona
        radio_id = random_radio.get_attribute("id")

        # Kliknij odpowiadający <label>
        select_radio_button(radio_id)


        click_button("download-file")

        # Weryfikacja pobrania pliku (zakładamy, że strona zawiera wskaźnik sukcesu)
        time.sleep(2)  # Poczekaj na zakończenie akcji
        # self.assertIn("application/pdf", driver.execute_script("return document.contentType"))
        # Weryfikacja, czy odpowiedni plik PDF został pobrany
        download_dir = os.path.join(os.path.expanduser("~"), "Downloads")
        self.assertTrue(
            verify_file_download_with_criteria(
                directory=download_dir,
                name_contains="mapa",
                file_extension=".pdf",
                max_age_seconds=30
            ),
            "Nie znaleziono odpowiedniego pliku PDF."
        )

    def tearDown(self):
        self.driver.quit()

if __name__ == "__main__":
    unittest.main()
