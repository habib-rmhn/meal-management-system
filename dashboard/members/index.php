<?php
$pageTitle = "Members and Meal";
$memClass = "active";
$cstlClass = "/assets/css/members.css";
require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-header.php';
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

$result = $conn->query("SELECT * FROM members ORDER BY id ASC");
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}
?>

<div class="table-wrapper">
<table class="meal-table">
    <thead>
        <tr>
            <th class="sticky-col"> </th>
            <?php foreach ($members as $index => $m): ?>
                <th>
                    <?= htmlspecialchars($m['name']) ?>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>

    <tbody>
        <!-- PAYMENT ROW -->
        <tr class="paid-row">
            <td class="sticky-col"><strong>Paid (৳) <i class="fa-solid fa-arrow-right"></i></strong></td>
            <?php foreach ($members as $m): ?>
                <td contenteditable="true"
                    data-id="<?= $m['id'] ?>"
                    data-field="payment"
                    class="editable">
                    <?= $m['payment'] ?>
                </td>
            <?php endforeach; ?>
        </tr>

        <!-- DAY ROWS -->
        <?php for ($day = 1; $day <= 31; $day++): ?>
        <tr>
            <td class="sticky-col">Day <?= $day ?></td>
            <?php foreach ($members as $m): ?>
                <td contenteditable="true"
                    data-id="<?= $m['id'] ?>"
                    data-field="day_<?= $day ?>"
                    class="editable">
                    <?= $m["day_$day"] ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
</div>

<!-- Floating Manage Members Button -->
<div class="add-member-btn" id="openManageModal" title="Manage Members">
    <i class="fa-solid fa-users-gear"></i>
</div>

<!-- MANAGE MEMBERS MODAL  -->
<div class="modal-overlay" id="manageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="manageModalTitle"><i class="fa-solid fa-users-gear"></i> Manage Members</h3>
            <button class="close-modal" id="closeManageModal">&times;</button>
        </div>
        <div class="modal-body">

            <!-- TABS -->
            <div class="member-tabs">
                <button class="member-tab active" data-tab="add">
                    <i class="fa-solid fa-user-plus"></i> Add Member
                </button>
                <button class="member-tab" data-tab="delete">
                    <i class="fa-solid fa-user-minus"></i> Remove Member
                </button>
            </div>

            <!-- ADD TAB -->
            <div class="tab-content" id="tab-add">
                <div class="form-group">
                    <label for="memberName"><i class="fa-solid fa-user"></i> Full Name <span style="color:#dc2626">*</span></label>
                    <input type="text" id="memberName" placeholder="e.g. Rahim Uddin" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="memberNumber"><i class="fa-solid fa-phone"></i> Phone Number</label>
                    <input type="text" id="memberNumber" placeholder="e.g. 01XXXXXXXXX" autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveNewMember">
                        <i class="fa-solid fa-user-plus"></i> Add Member
                    </button>
                </div>
            </div>

            <!-- DELETE TAB -->
            <div class="tab-content" id="tab-delete" style="display:none;">
                <p class="delete-hint">Select a member to remove from the system. <strong>This cannot be undone.</strong></p>
                <div class="member-list" id="memberListForDelete">
                    <?php foreach ($members as $m): ?>
                        <div class="member-list-item" data-id="<?= $m['id'] ?>" data-name="<?= htmlspecialchars($m['name']) ?>">
                            <div class="member-avatar"><?= strtoupper(substr($m['name'], 0, 1)) ?></div>
                            <div class="member-info">
                                <span class="member-name-text"><?= htmlspecialchars($m['name']) ?></span>
                                <span class="member-number-text"><?= $m['number'] ? htmlspecialchars($m['number']) : 'No number' ?></span>
                            </div>
                            <button class="btn-delete-member" data-id="<?= $m['id'] ?>" data-name="<?= htmlspecialchars($m['name']) ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($members)): ?>
                        <div class="no-members-msg">No members found.</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal-overlay" id="deleteMemberModal">
    <div class="modal-content confirmation-modal">
        <div class="modal-header">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Confirm Remove</h3>
            <button class="close-modal" id="closeDeleteMemberModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="confirmation-text">
                <p>Are you sure you want to remove <strong id="deleteMemberName">this member</strong>?</p>
                <p><strong>All their meal data will be permanently deleted.</strong></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="confirmDeleteMember">
                <i class="fa-solid fa-trash"></i> Remove Member
            </button>
        </div>
    </div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="memberToastContainer"></div>

<script src="/assets/js/members.js"></script>
<script src="/assets/js/manage-members.js"></script>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-footer.php';
?>