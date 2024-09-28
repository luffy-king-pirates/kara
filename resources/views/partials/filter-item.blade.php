<div id="filter-form" class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Filter Items</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap">
            <!-- ID Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-id">ID</label>
                <input type="text" id="filter-id" name="filter-id" class="form-control" placeholder="ID">
            </div>

            <!-- Item Code Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-item-code">Item Code</label>
                <input type="text" id="filter-item-code" name="filter-item-code" class="form-control" placeholder="Item Code">
            </div>

            <!-- Item Name Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-item-name">Item Name</label>
                <input type="text" id="filter-item-name" name="filter-item-name" class="form-control" placeholder="Item Name">
            </div>

            <!-- Category Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-category">Category</label>
                <select id="filter-category" name="filter-category" class="form-control">
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->categorie_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Brand Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-brand">Brand</label>
                <select id="filter-brand" name="filter-brand" class="form-control">
                    <option value="">Select Brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Item Size Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-item-size">Size</label>
                <input type="text" id="filter-item-size" name="filter-item-size" class="form-control" placeholder="Size">
            </div>

            <!-- Created By Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-created-by">Created By</label>
                <select id="filter-created-by" name="filter-created-by" class="form-control">
                    <option value="">Select Creator</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Updated By Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-updated-by">Updated By</label>
                <select id="filter-updated-by" name="filter-updated-by" class="form-control">
                    <option value="">Select Updater</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Created At Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-created-at">Created At</label>
                <input type="date" id="filter-created-at" name="filter-created-at" class="form-control">
            </div>

            <!-- Updated At Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-updated-at">Updated At</label>
                <input type="date" id="filter-updated-at" name="filter-updated-at" class="form-control">
            </div>
        </div>
    </div>
</div>
