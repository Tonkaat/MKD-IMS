<?php
  $page_title = 'All Group';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
  $all_groups = find_all('user_groups');
?>
<?php include_once('layouts/header.php'); ?>
<div class="container py-4">
  <div class="mb-3">
    <?php echo display_msg($msg); ?>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
      <h5 class="mb-0">
        <i class="bi bi-people-fill me-2"></i> Groups
      </h5>
      <a href="add_group.php" class="btn btn-light btn-sm rounded-pill">
        <i class="bi bi-plus-circle me-1"></i> Add New Group
      </a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Group Name</th>
              <th class="text-center" style="width: 20%;">Group Level</th>
              <th class="text-center" style="width: 15%;">Status</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_groups as $a_group): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td><?php echo remove_junk(ucwords($a_group['group_name'])) ?></td>
                <td class="text-center"><?php echo remove_junk(ucwords($a_group['group_level'])) ?></td>
                <td class="text-center">
                  <?php if ($a_group['group_status'] === '1'): ?>
                    <span class="badge bg-success">Active</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Deactive</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <a href="edit_group.php?id=<?php echo (int)$a_group['id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="delete_group.php?id=<?php echo (int)$a_group['id']; ?>" class="btn btn-sm btn-outline-danger" title="Remove">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

  <?php include_once('layouts/footer.php'); ?>
