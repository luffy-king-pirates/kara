<div id="filter-form" class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Filter Records</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap">
            <!-- ID Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-id">ID</label>
                <input type="text" id="filter-id" name="filter-id" class="form-control" placeholder="ID">
            </div>

            <!-- Month Filter -->
            <div class="form-group col-md-4 mb-3">
                <label for="filter-month-name">Month</label>
                <select id="filter-month-name" name="filter-month-name" class="form-control">
                    <option value="">Select Month</option>
                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                        <option value="{{ $month }}">{{ $month }}</option>
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
        </div>
    </div>
</div>
