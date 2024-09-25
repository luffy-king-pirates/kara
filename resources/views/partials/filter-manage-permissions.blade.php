<div id="filter-form" class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Filter Permission</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap">
            <!-- Role Name Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-role-name">Role Name</label>
                <input type="text" id="filter-role-name" name="filter-role-name" class="form-control" placeholder="Role Name">
            </div>

            <!-- Page Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-page">Page</label>
                <input type="text" id="filter-page" name="filter-page" class="form-control" placeholder="Page">
            </div>

            <!-- Action Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-action">Action</label>
                <select id="filter-action" name="filter-action" class="form-control">
                    <option value="">Select Action</option>
                    <option value="create">Create</option>
                    <option value="read">Read</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                </select>
            </div>
        </div>

    </div>
</div>
