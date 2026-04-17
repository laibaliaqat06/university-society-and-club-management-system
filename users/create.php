<?php
require_once '../includes/header.php';

// Fetch Roles
$roles = $pdo->query("SELECT * FROM sys_roles")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        trim($_POST['name']),
        trim($_POST['email']),
        password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
        $_POST['role'],
        trim($_POST['identity_no']),
        trim($_POST['registration_no'])
    ];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, identity_no, registration_no) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute($data);
        echo "<script>alert('User created successfully!'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Add New User</h3>
    </div>
    <div class="card-body">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Role</label>
                    <select name="role" class="form-select" required>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['role_key'] ?>"><?= $r['role_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Identity / CNIC</label>
                    <input type="text" name="identity_no" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Registration No</label>
                    <input type="text" name="registration_no" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
