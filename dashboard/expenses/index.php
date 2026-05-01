<?php
$pageTitle = "Expenses";
$expClass = "active";
$cstlClass = "/assets/css/expenses.css";

require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-header.php';
require $_SERVER['DOCUMENT_ROOT'] . '/auth/db.php';

/* FETCH EXPENSES */
$result = $conn->query("SELECT * FROM expenses ORDER BY date ASC");
?>

<div class="table-wrapper">
    <table class="expence-table">
        <thead>
            <tr>
                <th></th>
                <th>SL</th>
                <th>Date</th>
                <th>Expenses</th>
                <th>Comment</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            $sl = 1;
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td></td>
                    <td><?= $sl++; ?></td>
                    <td><?= date('j M Y', strtotime($row['date'])); ?></td>
                    <td><?= htmlspecialchars($row['amount']); ?></td>
                    <td><?= htmlspecialchars($row['comment']); ?></td>
                    <td class="action-icons">
                        <i class="fa-solid fa-pen-to-square edit-icon" data-id="<?= $row['id']; ?>"></i>
                        <i class="fa-solid fa-trash delete-icon" data-id="<?= $row['id']; ?>"></i>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="6" style="text-align:center;">No expenses found</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Floating Plus Button -->
<div class="add-expense-btn">
    <i class="fa-solid fa-plus"></i>
</div>

<!-- ADD/EDIT EXPENSE MODAL -->
<div class="modal-overlay" id="expenseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Expense</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="expenseForm">
                <input type="hidden" id="expenseId" name="id">
                
                <div class="form-group">
                    <label for="date"><i class="fa-solid fa-calendar"></i> Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="amount"><i class="fa-solid fa-money-bill-wave"></i> Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" placeholder="Enter amount" required>
                </div>
                
                <div class="form-group">
                    <label for="comment"><i class="fa-solid fa-comment"></i> Comment</label>
                    <textarea id="comment" name="comment" placeholder="Enter comment (optional)"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <!--<button type="button" class="btn btn-cancel close-modal">Cancel</button>-->
            <button type="button" class="btn btn-primary" id="saveExpense">Add Expense</button>
        </div>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content confirmation-modal">
        <div class="modal-header">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Confirm Delete</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="confirmation-text">
                <p>Are you sure you want to delete this expense?</p>
                <p><strong>This action cannot be undone.</strong></p>
            </div>
        </div>
        <div class="modal-footer">
            <!--<button type="button" class="btn btn-cancel close-modal">Cancel</button>-->
            <button type="button" class="btn btn-danger" id="confirmDelete">Delete Expense</button>
        </div>
    </div>
</div>

<!-- TOAST NOTIFICATION CONTAINER -->
<div class="toast-container"></div>

<script src="/assets/js/expenses.js"></script>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/assets/dash-footer.php';
?>