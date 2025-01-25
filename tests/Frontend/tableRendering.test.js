import $ from 'jquery';
global.$ = $;
import 'datatables.net';
import proj4 from 'proj4';
global.proj4 = proj4;
const helpersJS = require('../../public/js/helpers');
const tableJS = require('../../public/js/table');

describe('DataTables AJAX Integration Test with jQuery AJAX', () => {
  let points = [];
  const columnsConfig = [
    { width: '10%' },
    { width: '25%' },
    { width: '10%' },
    { width: '10%' },
    { width: '10%' }, 
    null,
    null
];
const columnDefsConfig =[
    { responsivePriority: 1, targets: 0 },
    { responsivePriority: 5, targets: 1 },
    { responsivePriority: 6, targets: 2 },
    { responsivePriority: 2, targets: 3 },
    { responsivePriority: 2, targets: 4 },
    { responsivePriority: 1, targets: 5 },
    { responsivePriority: 1, targets: 6 },
    { orderable: false, targets: [5, 6]}
];

  beforeEach(() => {
    document.body.innerHTML = `
      <table class="table table-hover" id="table">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Kod</th>
                    <th>Opis</th>
                    <th>Obszar</th>
                    <th>Easting</th>
                    <th>Northing</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>`;

    // Mockowanie jQuery AJAX
    $.ajax = jest.fn().mockImplementation(({ success }) => {
      success([
        {"id":1,"code":"31","description":"kamień","easting":467854.23,"northing":739890.04,"pointVirtual":0,"url":"http://bit.ly/iOrien?q=QkNPfDJ8MzE=","areas":[{"id":1,"name":"Grabówek"}],"tags":[{"id":16,"name":"Mrowisko"},{"id":60,"name":"Rurociąg"}]},
        {"id":2,"code":"32","description":"rozwidlenie drogi i ścieżki","easting":468014.71,"northing":739591.07,"pointVirtual":0,"url":"http://bit.ly/iOrien?q=QkNPfDJ8MzI=","areas":[{"id":1,"name":"Grabówek"}],"tags":[{"id":8,"name":"Suchy rów"},{"id":55,"name":"Płot"}]}
      ]);
    });
  });

  test('should load data from backend and display it in DataTable', (done) => {
      // $.ajax({
        // Wywołanie jQuery AJAX
        $.ajax({
          url: '/api/points',
          method: 'GET',
          success: (result) => {
            points = result;

            const rows = points.map(point => `
              <tr class="" data-id="${point.id}" id="${point.id}">
                  <td>${point.code}</td>
                  <td>${point.description}</td>
                  <td>${helpersJS.getAreaNames(point.areas)}</td>
                  <td>${point.easting}</td>
                  <td>${point.northing}</td>
                  <td><button data-id="${point.id}" class="btn btn-warning btn-sm w-100 edit-btn">Edytuj</button></td>
                  <td><button data-id="${point.id}" class="btn btn-danger btn-sm w-100 delete-btn">Usuń</button></td>
              </tr>
          `).join('');

          tableJS.populateTable(rows, columnsConfig, columnDefsConfig);




    

    // Poczekaj na zakończenie działania DataTables i sprawdź liczbę wierszy
    setTimeout(() => {
      expect($('#table tbody tr').length).toBe(2);

      // Sprawdź zawartość pierwszego wiersza
      const firstRowText = $('#table tbody tr:eq(0)').text();
      expect(firstRowText).toContain('31');
      expect(firstRowText).toContain('kamień');

      done();
    }, 500); // Krótki timeout, aby DataTables zdążyło się załadować
  }
        });
});
});