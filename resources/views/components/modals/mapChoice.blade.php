<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Wybierz mapę podkładową</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
            </div>
            <div class="modal-body">
                <form id="mapSelectionForm">
                    <!-- Lista map zostanie dodana dynamicznie tutaj -->
                    <div id="mapList"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="download-file">Pobierz</button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Anuluj</button>
            </div>
        </div>
    </div>
</div>