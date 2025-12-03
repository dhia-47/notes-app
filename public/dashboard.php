<?php 
include "header.php"; 
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ------------------------------
   BASIC STATS
--------------------------------*/

// Total notes
$countNotes = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM notes WHERE user_id=$user_id")
)['total'];

// Total categories
$countCategories = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(DISTINCT category) AS total FROM notes WHERE user_id=$user_id AND category != ''")
)['total'];

/* ------------------------------
   NOTES PER CATEGORY
--------------------------------*/
$catQuery = mysqli_query($conn,
    "SELECT category, COUNT(*) AS total 
     FROM notes 
     WHERE user_id=$user_id AND category != ''
     GROUP BY category");

$chartCategories = [];
$chartCatValues  = [];

while ($row = mysqli_fetch_assoc($catQuery)) {
    $chartCategories[] = $row['category'];
    $chartCatValues[]  = $row['total'];
}

/* ------------------------------
   NOTES PER MONTH
--------------------------------*/
$monthQuery = mysqli_query($conn,
    "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
     FROM notes 
     WHERE user_id=$user_id
     GROUP BY month
     ORDER BY month ASC"
);

$chartMonths = [];
$chartMonthValues = [];

while ($m = mysqli_fetch_assoc($monthQuery)) {
    $chartMonths[] = $m['month'];
    $chartMonthValues[] = $m['total'];
}

/* ------------------------------
   LAST NOTES
--------------------------------*/
$lastNotes = mysqli_query(
    $conn,
    "SELECT * FROM notes 
     WHERE user_id=$user_id 
     ORDER BY created_at DESC 
     LIMIT 5"
);
?>

<style>
.stat-card {
    border-radius: 14px;
    transition: 0.25s;
    background: #ffffff;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}
.section-title {
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}
.chart-card {
    border-radius: 12px;
    padding: 20px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}
.table-card {
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}
</style>

<h3 class="mb-4">📊 Dashboard</h3>

<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card text-center p-4 shadow-sm">
            <h6 class="text-muted">Total Notes</h6>
            <div class="display-5 fw-bold text-primary"><?= $countNotes ?></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card text-center p-4 shadow-sm">
            <h6 class="text-muted">Total Categories</h6>
            <div class="display-5 fw-bold text-success"><?= $countCategories ?></div>
        </div>
    </div>
</div>

<!-- Recent Notes Table -->
<div class="card table-card p-4 mt-3">
    <h5 class="section-title">📝 Recent Notes</h5>

    <table class="table table-hover table-bordered mt-2">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Created</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($n = mysqli_fetch_assoc($lastNotes)): ?>
                <tr>
                    <td><?= $n['title'] ?></td>
                    <td><?= $n['category'] ?></td>
                    <td><?= $n['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Charts Under the Table -->
<div class="row mt-4">

    <!-- Category Chart -->
    <div class="col-md-6 mb-4">
        <div class="chart-card">
            <h5 class="text-center mb-3">📂 Notes by Category</h5>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="col-md-6 mb-4">
        <div class="chart-card">
            <h5 class="text-center mb-3">📅 Notes per Month</h5>
            <canvas id="monthChart"></canvas>
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Data from PHP
const categories = <?= json_encode($chartCategories) ?>;
const categoryValues = <?= json_encode($chartCatValues) ?>;
const months = <?= json_encode($chartMonths) ?>;
const monthValues = <?= json_encode($chartMonthValues) ?>;

// Pie Chart
new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: {
        labels: categories,
        datasets: [{
            data: categoryValues,
            borderWidth: 1
        }]
    }
});

// Line Chart
new Chart(document.getElementById('monthChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Notes',
            data: monthValues,
            tension: 0.4,
            borderWidth: 2
        }]
    }
});
</script>

<?php include "footer.php"; ?>
