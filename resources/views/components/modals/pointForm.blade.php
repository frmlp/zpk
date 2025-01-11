<!-- Point Modal -->
<div class="modal fade" id="pointModal" tabindex="-1" aria-labelledby="pointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pointModalLabel">Punkt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pointForm">
                    @csrf
                     <div class="row">
                        <!-- Pole Kod -->
                        <div class="col mb-3">
                            <label for="pointCode" class="form-label">Kod</label>
                            <input type="text" class="form-control" id="pointCode" name="code">
                        </div>
                        <!-- Punkt wirtualny -->
                        <div class="col mb-3 text-center">
                            <label class="form-label d-block" for="pointVirtual">Punkt wirtualny</label>
                            <input type="hidden" name="pointVirtual" value="0">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="pointVirtual" name="pointVirtual" value="1">
                            </div>
                        </div>
                     </div>
                    
                    <!-- Pole Opis -->
                    <div class="mb-3">
                        <label for="pointDescription" class="form-label">Opis</label>
                        <input type="text" class="form-control" id="pointDescription" name="description">
                    </div>
                    <!-- Rodzaj współrzędnych -->
                    <div class="mb-3">
                        <label class="form-label">Rodzaj współrzędnych</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="coordinateType" id="coordinateEPSG2180" value="EPSG2180" checked>
                                    <label class="form-check-label" for="coordinateEPSG2180">EPSG:2180</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="coordinateType" id="coordinateWSG84" value="WSG84">
                                    <label class="form-check-label" for="coordinateWSG84">WSG:84</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- współrzędne EPSG:2180 -->
                    <div id="epsg2180Fields" class="coordinates row mb-3">
                        <div class="col">
                            <label for="pointEasting" class="form-label">Easting</label>
                            <input type="text" class="form-control" id="pointEasting" name="easting">
                        </div>
                        <div class="col">
                            <label for="pointNorthing" class="form-label">Northing</label>
                            <input type="text" class="form-control" id="pointNorthing" name="northing">
                        </div>
                    </div>
                    <!-- współrzędne WSG:84 -->
                    <div id="wsg84Fields" class="coordinates row mb-3" style="display:none;">
                        <div class="col">
                            <label for="pointLongitude" class="form-label">Długość</label>
                            <input type="text" class="form-control" id="pointLongitude" name="longitude">
                        </div>
                        <div class="col">
                            <label for="pointLatitude" class="form-label">Szerokość</label>
                            <input type="text" class="form-control" id="pointLatitude" name="latitude">
                        </div>
                    </div>
                    
                    
                    <!-- Obszar -->
                    <div class="mb-3">
                        <label for="areaId" class="form-label">Obszar</label>
                        <select class="form-select" id="areaId" name="area_id">
                            <option value="1">Grabówek</option>
                            <option value="2">Chylonia</option>
                        </select>
                    </div>
                    <!-- Url kodu QR -->
                    <div class="mb-3">
                        <label class="form-label">Url kodu QR</label>
                        <input type="text" class="form-control" id="pointUrl" name="url">
                    </div>
                    <!-- Tagi -->
                    <div class="mb-3">
                        <label class="form-label">Tagi</label>
                        <div id="dropdown-container" class="mb-3"></div>
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