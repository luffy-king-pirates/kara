<!-- Button to trigger the modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
  Upload Excel File
</button>

<!-- Modal Structure -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadModalLabel">Upload Excel File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body: File Upload Form -->
      <div class="modal-body">
        <form id="fileUploadForm" action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label for="file">Select Excel File</label>
            <input type="file" name="file" id="file" class="form-control" required>
          </div>
        </form>
      </div>

      <!-- Modal Footer: Buttons -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="fileUploadForm">Upload File</button>
      </div>
    </div>
  </div>
</div>
