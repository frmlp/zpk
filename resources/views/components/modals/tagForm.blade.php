<div class="modal fade" id="tagModal" tabindex="-1" aria-labelledby="pointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tagModalLabel">Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tagForm">
                    @csrf
                    <div id="alertMessage" class="alert alert-danger"></div>
                    <!-- Pole Kod -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nazwa</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <!-- Kody przypisanych punktów -->
                    <div class="mb-3" id="pointsContainerWrapper">
                        <label class="form-label">Kody przypisanych punktów</label>
                        <div id="pointsContainer" class="d-flex flex-wrap justify-content-start"></div>
                    </div>

                    <!-- Przyciski -->
                     <div class="row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100" id="saveChanges">Zapisz</button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Anuluj</button>
                        </div>
                        
                        
                     </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>