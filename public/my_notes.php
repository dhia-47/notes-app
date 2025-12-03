<?php 
include "header.php"; 
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ---- Input Filters ----
$search   = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : "";

// Build WHERE clause
$where = "user_id = $user_id";

if (!empty($search)) {
    $where .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
}
if (!empty($category)) {
    $where .= " AND category = '$category'";
}

// Fetch categories
$categories = mysqli_query($conn, 
    "SELECT DISTINCT category FROM notes WHERE user_id=$user_id AND category IS NOT NULL AND category != ''"
);

// Fetch notes
$notes = mysqli_query($conn, "SELECT * FROM notes WHERE $where ORDER BY created_at DESC");
?>

<!-- ⭐ Custom Styles for Cards & Badges ⭐ -->
<style>
.note-card {
    transition: 0.2s;
    border-radius: 10px;
}
.note-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.badge-category {
    background: #eef2ff;
    color: #4f46e5;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 6px;
}
</style>

<h3 class="mb-4">My Notes</h3>

<!-- Top Button -->
<div class="d-flex justify-content-between mb-4">
    <a href="note_create.php" class="btn btn-success">+ Add New Note</a>
</div>

<!-- Search & Filter -->
<div class="card shadow-sm mb-4 p-3">
    <form class="row g-3" method="GET">
        
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" 
                   placeholder="Search by title or content..." 
                   value="<?= $search ?>">
        </div>

        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">All Categories</option>

                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= $cat['category'] ?>" <?= ($category === $cat['category']) ? 'selected' : '' ?>>
                        <?= ucfirst($cat['category']) ?>
                    </option>
                <?php endwhile; ?>

            </select>
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary w-100">Apply Filter</button>
        </div>

    </form>
</div>

<!-- Notes Cards -->
<div class="row">

<?php if (mysqli_num_rows($notes) == 0): ?>

    <div class="col-12">
        <div class="alert alert-warning text-center p-4">
            <h5>No notes found</h5>
            <p>Try changing your search or filter options.</p>
        </div>
    </div>

<?php else: ?>

    <?php while ($row = mysqli_fetch_assoc($notes)): ?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm note-card h-100">

            <div class="card-body">
                <h5 class="card-title"><?= $row['title'] ?></h5>

                <?php if (!empty($row['category'])): ?>
                <span class="badge-category mb-2 d-inline-block">
                    <?= $row['category'] ?>
                </span>
                <?php endif; ?>

                <p class="card-text mt-2" style="min-height: 60px;">
                    <?= nl2br(substr($row['content'], 0, 150)) ?>...
                </p>

                <small class="text-muted">
                    Created: <?= date("Y-m-d", strtotime($row['created_at'])) ?>
                </small>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="note_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                    Edit
                </a>
                <a href="note_delete.php?id=<?= $row['id'] ?>" 
                   onclick="return confirm('Delete this note?')" 
                   class="btn btn-sm btn-danger">
                    Delete
                </a>
            </div>

        </div>
    </div>
    <?php endwhile; ?>

<?php endif; ?>

</div>

<?php include "footer.php"; ?>
