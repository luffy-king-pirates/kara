 <div id="filter-form" class="card mb-3">
     <div class="card-header">
         <h5 class="card-title mb-0">Filter Roles</h5>
     </div>
     <div class="card-body">
         <div class="d-flex flex-wrap">
             <!-- ID Filter -->
             <div class="form-group col-md-4 mb-3">
                 <label for="filter-id">ID</label>
                 <input type="text" id="filter-id" name="filter-id" class="form-control" placeholder="ID">
             </div>

             <!-- Role Name Filter -->
             <div class="form-group col-md-4 mb-3">
                 <label for="filter-role-name">Role Name</label>
                 <input type="text" id="filter-role-name" name="filter-role-name" class="form-control"
                     placeholder="Role Name">
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
