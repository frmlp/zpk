<!-- Path Modal -->
<div class="modal fade" id="pathModal" tabindex="-1" aria-labelledby="pathModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pathModalLabel">Trasa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pathForm">
                    @csrf
                    <div id="alertMessage" class="alert alert-danger"></div>
                    {{-- <div class="m-3"> --}}
                        <!-- Nazwa -->
                        <div class="mb-3">
                            <label for="pathName" class="form-label">Nazwa</label>
                            <input type="text" class="form-control" id="pathName" name="name" required>
                        </div>
                        <!-- Przebieg trasy -->
                        <div class="mb-3">
                            <label for="pathName" class="form-label">Przebieg trasy:</label>
                            <div id="dropdown-container" class="">
                            </div>
                        </div>
                        <!-- Mapa -->
                        <div class="mb-3" id="map-modal"></div>
                    {{-- </div> --}}

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