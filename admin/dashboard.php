<?php
session_start();
include '../includes/db_connect.php';

// AJAX Handler
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $action = $_POST['ajax_action'];

    if ($action === 'toggle_visibility') {
        $id = (int) $_POST['id'];
        $current = (int) $_POST['status'];
        $new = $current ? 0 : 1;

        $stmt = $conn->prepare("UPDATE menu_items SET is_visible = ? WHERE id = ?");
        $stmt->bind_param("ii", $new, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'new_status' => $new]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    if ($action === 'delete_menu') {
        $id = (int) $_POST['id'];
        if ($conn->query("DELETE FROM menu_items WHERE id=$id")) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'editor';

// Actions
if (isset($_GET['delete_user']) && $role === 'admin') {
    $del_id = (int) $_GET['delete_user'];
    if ($del_id != $user_id) {
        $conn->query("DELETE FROM users WHERE id=$del_id");
        header("Location: dashboard.php?tab=users&msg=deleted");
        exit;
    }
}

if (isset($_GET['delete_post'])) {
    $del_id = (int) $_GET['delete_post'];
    $conn->query("DELETE FROM posts WHERE id=$del_id");
    header("Location: dashboard.php?msg=post_deleted");
    exit;
}

if (isset($_GET['unpublish'])) {
    $post_id = (int) $_GET['unpublish'];
    $conn->query("UPDATE posts SET status='draft' WHERE id=$post_id");
    header("Location: dashboard.php?msg=drafts");
    exit;
}

if (isset($_GET['publish'])) {
    $post_id = (int) $_GET['publish'];
    $conn->query("UPDATE posts SET status='published' WHERE id=$post_id");
    header("Location: dashboard.php?msg=published");
    exit;
}

// Stats
$stats_pub = $conn->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetch_row()[0];
$stats_draft = $conn->query("SELECT COUNT(*) FROM posts WHERE status='draft'")->fetch_row()[0];
$stats_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];

// Menu Items count (with table existence check)
$stats_menu = 0;
$menu_check = $conn->query("SHOW TABLES LIKE 'menu_items'");
if ($menu_check && $menu_check->num_rows > 0) {
    $stats_menu = $conn->query("SELECT COUNT(*) FROM menu_items")->fetch_row()[0];
}

// Tab & Filters
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'blogs';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Posts with author full_name
$sql = "SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE 1=1";
if ($filter == 'published')
    $sql .= " AND p.status = 'published'";
if ($filter == 'drafts' || $filter == 'draft')
    $sql .= " AND p.status = 'draft'";
if ($filter == 'me')
    $sql .= " AND p.author_id = $user_id";
if (!empty($search))
    $sql .= " AND p.title LIKE '%$search%'";
$sql .= " ORDER BY p.created_at DESC";
$posts = $conn->query($sql);

// Users with full_name
$users = $conn->query("SELECT *, full_name as display_name FROM users ORDER BY id DESC");

// Menu Items Query
$menu_cat = isset($_GET['menu_cat']) ? $_GET['menu_cat'] : 'all';
$menu_search = isset($_GET['menu_search']) ? $conn->real_escape_string($_GET['menu_search']) : '';
$menu_sql = "SELECT * FROM menu_items WHERE 1=1";
if ($menu_cat !== 'all')
    $menu_sql .= " AND category = '$menu_cat'";
if (!empty($menu_search))
    $menu_sql .= " AND name LIKE '%$menu_search%'";
$menu_sql .= " ORDER BY created_at DESC";
$menu_items = null;
if ($menu_check && $menu_check->num_rows > 0) {
    $menu_items = $conn->query($menu_sql);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Danono's Admin</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Fredoka:wght@500;600&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .dashboard-wrapper {
            width: 100%;
            padding: 20px;
        }

        /* Dashboard Layout (Full Width) */
        .dashboard-layout {
            display: block;
            width: 100%;
            align-items: stretch;
            /* Equal heights */
        }

        /* Editor: Full width, hide sidebar */
        .dashboard-layout.editor-view {
            grid-template-columns: 1fr;
        }

        .dashboard-main {
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 100%;
        }

        .tabs-container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .tab-content.active {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .data-table-container {
            flex: 1;
            overflow-y: auto;
        }

        .dashboard-sidebar {
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        /* Toolbar row with proper spacing */
        /* Toolbar row with proper spacing */
        .toolbar-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: nowrap;
            /* Prevent wrapping */
        }

        .toolbar-row .filter-group {
            flex-shrink: 0;
            display: flex;
            gap: 10px;
            overflow-x: auto;
            max-width: 60%;
        }

        .toolbar-row .search-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-left: auto;
            flex-shrink: 0;
            width: auto;
        }

        .toolbar-row .search-container input {
            width: 280px;
            padding: 10px 15px;
            font-size: 14px;
        }

        .toolbar-row .search-container .btn {
            white-space: nowrap;
            flex-shrink: 0;
        }

        @media (max-width: 1024px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="dashboard-wrapper">

        <!-- Header -->
        <div class="admin-header">
            <div class="admin-header-left">
                <h1>Dashboard</h1>
                <span style="color: var(--gray); font-size: 14px;">Welcome, <?php echo htmlspecialchars($full_name); ?>
                    (<?php echo ucfirst($role); ?>)</span>
            </div>
            <div class="admin-header-right">
                <a href="../index.php" target="_blank" class="btn btn-secondary">
                    <i class="ph ph-globe"></i> View Site
                </a>
                <a href="logout.php" class="btn btn-secondary">
                    <i class="ph ph-sign-out"></i> Logout
                </a>
            </div>
        </div>

        <!-- Dashboard Layout -->
        <div class="dashboard-layout <?php echo ($role !== 'admin') ? 'editor-view' : ''; ?>">

            <!-- LEFT: Main Content -->
            <div class="dashboard-main">

                <!-- Stats Row (Role-Based with Flexible Grid) -->
                <div class="stats-row" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <?php if ($role === 'admin'): ?>
                        <div class="stat-card" style="flex: 1; min-width: 200px;">
                            <div class="stat-icon"><i class="ph ph-users-three"></i></div>
                            <div class="stat-info">
                                <h3><?php echo $stats_users; ?></h3>
                                <p>Total Users</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="stat-card" style="flex: 1; min-width: 200px;">
                        <div class="stat-icon"><i class="ph ph-article-medium"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $stats_pub; ?></h3>
                            <p>Total Blogs</p>
                        </div>
                    </div>
                    <div class="stat-card" style="flex: 1; min-width: 200px;">
                        <div class="stat-icon"><i class="ph ph-note-pencil"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $stats_draft; ?></h3>
                            <p>Active Drafts</p>
                        </div>
                    </div>
                    <div class="stat-card" style="flex: 1; min-width: 200px;">
                        <div class="stat-icon"><i class="ph ph-cookie"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $stats_menu; ?></h3>
                            <p>Menu Items</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs Container -->
                <div class="tabs-container">
                    <div class="tabs-header">
                        <button class="tab-btn <?php echo $tab == 'blogs' ? 'active' : ''; ?>"
                            onclick="location.href='?tab=blogs'">
                            <i class="ph ph-article"></i> Blogs
                        </button>
                        <?php if ($role === 'admin'): ?>
                            <button class="tab-btn <?php echo $tab == 'users' ? 'active' : ''; ?>"
                                onclick="location.href='?tab=users'">
                                <i class="ph ph-users"></i> Users
                            </button>
                        <?php endif; ?>
                        <button class="tab-btn <?php echo $tab == 'menu' ? 'active' : ''; ?>"
                            onclick="location.href='?tab=menu'">
                            <i class="ph ph-cookie"></i> Menu
                        </button>
                    </div>

                    <!-- BLOGS TAB -->
                    <div class="tab-content <?php echo $tab == 'blogs' ? 'active' : ''; ?>">

                        <!-- Toolbar: Filters + Search + New Post Button -->
                        <div class="toolbar-row">
                            <a href="?tab=blogs&filter=all"
                                class="filter-btn <?php echo $filter == 'all' ? 'active' : ''; ?>">All</a>
                            <a href="?tab=blogs&filter=published"
                                class="filter-btn <?php echo $filter == 'published' ? 'active' : ''; ?>">Published</a>
                            <a href="?tab=blogs&filter=drafts"
                                class="filter-btn <?php echo $filter == 'drafts' || $filter == 'draft' ? 'active' : ''; ?>">Drafts</a>
                            <a href="?tab=blogs&filter=me"
                                class="filter-btn <?php echo $filter == 'me' ? 'active' : ''; ?>">My Posts</a>

                            <div class="search-container">
                                <form>
                                    <input type="hidden" name="tab" value="blogs">
                                    <input type="text" name="search" placeholder="Search posts..."
                                        value="<?php echo htmlspecialchars($search); ?>" class="form-input">
                                </form>
                                <a href="post-create.php" class="btn btn-primary">
                                    <i class="ph ph-plus"></i> New Post
                                </a>
                            </div>
                        </div>

                        <!-- Posts Table -->
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="50"></th>
                                        <th>Title</th>
                                        <th width="100">Status</th>
                                        <th width="100">Date</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($posts && $posts->num_rows > 0): ?>
                                        <?php while ($row = $posts->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($row['featured_image'] && file_exists("../uploads/" . $row['featured_image'])): ?>
                                                        <img src="../uploads/<?php echo $row['featured_image']; ?>"
                                                            class="table-thumb" alt="">
                                                    <?php else: ?>
                                                        <div
                                                            style="width: 40px; height: 40px; background: #f3f4f6; border-radius: 4px;">
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong
                                                        style="display: block; color: var(--dark);"><?php echo htmlspecialchars($row['title']); ?></strong>
                                                    <span style="font-size: 11px; color: var(--gray);">by
                                                        <?php echo htmlspecialchars($row['author_name'] ?? 'Unknown'); ?></span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="status-badge <?php echo $row['status'] == 'published' ? 'status-published' : 'status-draft'; ?>">
                                                        <?php echo ucfirst($row['status']); ?>
                                                    </span>
                                                </td>
                                                <td style="font-size: 12px; color: var(--gray);">
                                                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                                </td>
                                                <td>
                                                    <div class="action-icons">
                                                        <a href="post-edit.php?id=<?php echo $row['id']; ?>" class="action-icon"
                                                            title="Edit">
                                                            <i class="ph ph-pencil-simple"></i>
                                                        </a>
                                                        <?php if ($row['status'] == 'published'): ?>
                                                            <a href="?unpublish=<?php echo $row['id']; ?>" class="action-icon"
                                                                title="Unpublish"
                                                                onclick="return confirmLink(event, 'Unpublish this post?');">
                                                                <i class="ph ph-eye-slash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="?publish=<?php echo $row['id']; ?>" class="action-icon"
                                                                title="Publish"
                                                                onclick="return confirmLink(event, 'Publish this post?');">
                                                                <i class="ph ph-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="?delete_post=<?php echo $row['id']; ?>"
                                                            class="action-icon danger" title="Delete"
                                                            onclick="return confirmLink(event, 'Delete this post permanently?');">
                                                            <i class="ph ph-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--gray);">
                                                No posts found. <a href="post-create.php"
                                                    style="color: var(--primary);">Create your first post</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- USERS TAB (Admin Only) -->
                    <?php if ($role === 'admin'): ?>
                        <div class="tab-content <?php echo $tab == 'users' ? 'active' : ''; ?>">
                            <!-- Toolbar: Filters + Search + Add User Button -->
                            <div class="toolbar-row">
                                <div class="filter-group">
                                    <button type="button" class="filter-btn active"
                                        onclick="filterUsers('all', this)">All</button>
                                    <button type="button" class="filter-btn"
                                        onclick="filterUsers('admin', this)">Admin</button>
                                    <button type="button" class="filter-btn"
                                        onclick="filterUsers('editor', this)">Editor</button>
                                </div>

                                <div class="search-container">
                                    <input type="text" id="userSearchInput" placeholder="Search users..." class="form-input"
                                        onkeyup="searchUsers()">
                                    <button type="button" class="btn btn-primary" onclick="openAddUserModal()">
                                        <i class="ph ph-plus"></i> Add User
                                    </button>
                                </div>
                            </div>

                            <!-- Users Table (Full Width) -->
                            <div class="data-table-container">
                                <table class="data-table" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($users)
                                            $users->data_seek(0);
                                        while ($users && $u = $users->fetch_assoc()):
                                            ?>
                                            <tr data-role="<?php echo strtolower($u['role']); ?>">
                                                <td><strong><?php echo htmlspecialchars($u['display_name']); ?></strong></td>
                                                <td style="color: var(--gray);"><?php echo htmlspecialchars($u['email']); ?>
                                                </td>
                                                <td>
                                                    <span class="status-badge"
                                                        style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;">
                                                        <?php echo ucfirst($u['role']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($u['id'] != $user_id): ?>
                                                        <div class="action-icons">
                                                            <button type="button" class="btn-action edit-user-btn"
                                                                data-id="<?php echo $u['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($u['display_name']); ?>"
                                                                data-email="<?php echo htmlspecialchars($u['email']); ?>"
                                                                data-role="<?php echo $u['role']; ?>" title="Edit">
                                                                <i class="ph ph-pencil-simple"></i>
                                                            </button>
                                                            <form action="code.php" method="POST" class="d-inline"
                                                                onsubmit="return confirmFormSubmit(event, 'Remove this user?');">
                                                                <input type="hidden" name="delete_user_id"
                                                                    value="<?php echo $u['id']; ?>">
                                                                <button type="submit" name="delete_user"
                                                                    class="btn-action btn-action-danger">
                                                                    <i class="ph ph-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php else: ?>
                                                        <span style="color: var(--gray); font-size: 12px;">You</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- MENU TAB -->
                    <div class="tab-content <?php echo $tab == 'menu' ? 'active' : ''; ?>">
                        <!-- Toolbar: Filters + Search + Add Item Button -->
                        <div class="toolbar-row">
                            <div class="filter-group">
                                <button type="button" class="filter-btn active"
                                    onclick="filterMenu('all', this)">All</button>
                                <button type="button" class="filter-btn"
                                    onclick="filterMenu('doughnuts', this)">Doughnuts</button>
                                <button type="button" class="filter-btn"
                                    onclick="filterMenu('brownies', this)">Brownies</button>
                                <button type="button" class="filter-btn"
                                    onclick="filterMenu('beverages', this)">Beverages</button>
                            </div>

                            <div class="search-container">
                                <input type="text" id="menuSearchInput" placeholder="Search menu..." class="form-input"
                                    onkeyup="searchMenu()">
                                <button type="button" class="btn btn-primary" onclick="openAddMenuModal()">
                                    <i class="ph ph-plus"></i> Add Item
                                </button>
                            </div>
                        </div>

                        <!-- Menu Table -->
                        <div class="data-table-container">
                            <table class="data-table" id="menuTable">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($menu_items && $menu_items->num_rows > 0): ?>
                                        <?php while ($m = $menu_items->fetch_assoc()): ?>
                                            <tr data-category="<?php echo strtolower($m['category']); ?>">
                                                <td>
                                                    <?php if ($m['image']): ?>
                                                        <img src="../uploads/<?php echo htmlspecialchars($m['image']); ?>" alt=""
                                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                                    <?php else: ?>
                                                        <div
                                                            style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="ph ph-image" style="color: #ccc;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                                                <td><span class="status-badge"
                                                        style="background: #FFF5EA; color: #EF7D32;"><?php echo $m['category']; ?></span>
                                                </td>
                                                <td><strong>₱<?php echo number_format($m['price'], 2); ?></strong></td>
                                                <td class="text-nowrap">
                                                    <div class="action-icons">
                                                        <!-- Visibility Toggle (AJAX) -->
                                                        <button type="button"
                                                            class="btn-action <?php echo (isset($m['is_visible']) && $m['is_visible'] == 0) ? 'btn-action-muted' : 'btn-action-success'; ?>"
                                                            title="<?php echo (isset($m['is_visible']) && $m['is_visible'] == 0) ? 'Show Item' : 'Hide Item'; ?>"
                                                            data-id="<?php echo $m['id']; ?>"
                                                            data-status="<?php echo isset($m['is_visible']) ? $m['is_visible'] : 1; ?>"
                                                            onclick="toggleMenuVisibility(this)">
                                                            <i
                                                                class="ph <?php echo (isset($m['is_visible']) && $m['is_visible'] == 0) ? 'ph-eye-slash' : 'ph-eye'; ?>"></i>
                                                        </button>

                                                        <!-- Edit Button -->
                                                        <button type="button" class="btn-action edit-menu-btn"
                                                            data-id="<?php echo $m['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($m['name']); ?>"
                                                            data-price="<?php echo $m['price']; ?>"
                                                            data-category="<?php echo $m['category']; ?>"
                                                            data-description="<?php echo htmlspecialchars($m['description']); ?>"
                                                            data-alt="<?php echo htmlspecialchars(isset($m['alt_text']) ? $m['alt_text'] : ''); ?>"
                                                            data-image="<?php echo htmlspecialchars($m['image']); ?>"
                                                            title="Edit">
                                                            <i class="ph ph-pencil-simple"></i>
                                                        </button>

                                                        <!-- Delete Button (AJAX) -->
                                                        <button type="button" class="btn-action btn-action-danger"
                                                            title="Delete Item"
                                                            onclick="deleteMenuItem(<?php echo $m['id']; ?>, this)">
                                                            <i class="ph ph-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--gray);">
                                                <i class="ph ph-cookie"
                                                    style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                                                No menu items found. <button type="button" onclick="openAddMenuModal()"
                                                    style="color: var(--primary); background: none; border: none; cursor: pointer;">Add
                                                    your first item</button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ======================== -->
            <!-- MODALS -->
            <!-- ======================== -->

            <!-- ADD USER MODAL -->
            <div id="addUserModal" class="modal-overlay" style="display: none;">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3><i class="ph ph-user-plus"></i> Add New User</h3>
                        <button type="button" class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
                    </div>
                    <form action="code.php" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="field-label">Full Name *</label>
                                <input type="text" name="full_name" class="form-input" placeholder="John Doe" required>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Email *</label>
                                <input type="email" name="email" class="form-input" placeholder="john@example.com"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Password *</label>
                                <input type="password" name="password" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Role *</label>
                                <select name="role" class="form-input" required>
                                    <option value="editor">Editor</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                onclick="closeModal('addUserModal')">Cancel</button>
                            <button type="submit" name="save_user" class="btn btn-primary"><i class="ph ph-plus"></i>
                                Add User</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- EDIT USER MODAL -->
            <div id="editUserModal" class="modal-overlay" style="display: none;">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3><i class="ph ph-pencil-simple"></i> Edit User</h3>
                        <button type="button" class="modal-close" onclick="closeModal('editUserModal')">&times;</button>
                    </div>
                    <form action="code.php" method="POST">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="field-label">Full Name *</label>
                                <input type="text" name="full_name" id="edit_user_name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Email *</label>
                                <input type="email" name="email" id="edit_user_email" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="field-label">New Password</label>
                                <input type="password" name="password" class="form-input"
                                    placeholder="Leave blank to keep current">
                                <small style="color: var(--gray);">Only fill if changing password</small>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Role *</label>
                                <select name="role" id="edit_user_role" class="form-input" required>
                                    <option value="editor">Editor</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                onclick="closeModal('editUserModal')">Cancel</button>
                            <button type="submit" name="update_user" class="btn btn-primary"><i
                                    class="ph ph-floppy-disk"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ADD MENU ITEM MODAL -->
            <div id="addMenuModal" class="modal-overlay" style="display: none;">
                <div class="modal-container modal-lg">
                    <div class="modal-header">
                        <h3><i class="ph ph-plus-circle"></i> Add Menu Item</h3>
                        <button type="button" class="modal-close" onclick="closeModal('addMenuModal')">&times;</button>
                    </div>
                    <form action="code.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">Item Name *</label>
                                    <input type="text" name="name" class="form-input" placeholder="e.g. Glazed Doughnut"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label class="field-label">Price (₱) *</label>
                                    <input type="number" name="price" step="0.01" min="0" class="form-input"
                                        placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Category *</label>
                                <select name="category" class="form-input" required>
                                    <option value="">Select Category</option>
                                    <option value="Doughnuts">Doughnuts</option>
                                    <option value="Brownies">Brownies</option>
                                    <option value="Coffee">Coffee</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Description</label>
                                <textarea name="description" class="form-input" rows="3"
                                    placeholder="Short description..."></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">Alt Text</label>
                                    <input type="text" name="alt_text" class="form-input"
                                        placeholder="Image description for accessibility">
                                </div>
                                <div class="form-group">
                                    <label class="field-label">Image</label>
                                    <input type="file" name="image" class="form-input" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                onclick="closeModal('addMenuModal')">Cancel</button>
                            <button type="submit" name="save_menu" class="btn btn-primary"><i class="ph ph-plus"></i>
                                Add Item</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- EDIT MENU ITEM MODAL -->
            <div id="editMenuModal" class="modal-overlay" style="display: none;">
                <div class="modal-container modal-lg">
                    <div class="modal-header">
                        <h3><i class="ph ph-pencil-simple"></i> Edit Menu Item</h3>
                        <button type="button" class="modal-close" onclick="closeModal('editMenuModal')">&times;</button>
                    </div>
                    <form action="code.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="menu_id" id="edit_menu_id">
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">Item Name *</label>
                                    <input type="text" name="name" id="edit_menu_name" class="form-input" required>
                                </div>
                                <div class="form-group">
                                    <label class="field-label">Price (₱) *</label>
                                    <input type="number" name="price" id="edit_menu_price" step="0.01" min="0"
                                        class="form-input" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Category *</label>
                                <select name="category" id="edit_menu_category" class="form-input" required>
                                    <option value="Doughnuts">Doughnuts</option>
                                    <option value="Brownies">Brownies</option>
                                    <option value="Coffee">Coffee</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="field-label">Description</label>
                                <textarea name="description" id="edit_menu_description" class="form-input"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="field-label">Alt Text</label>
                                    <input type="text" name="alt_text" id="edit_menu_alt" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="field-label">Image</label>
                                    <input type="file" name="image" class="form-input" accept="image/*">
                                    <small id="edit_menu_current_image" style="color: var(--gray);"></small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                onclick="closeModal('editMenuModal')">Cancel</button>
                            <button type="submit" name="update_menu" class="btn btn-primary"><i
                                    class="ph ph-floppy-disk"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ======================== -->
            <!-- MODAL STYLES -->
            <!-- ======================== -->
            <style>
                .modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .modal-container {
                    background: white;
                    border-radius: 12px;
                    width: 100%;
                    max-width: 450px;
                    max-height: 90vh;
                    overflow-y: auto;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
                }

                .modal-container.modal-lg {
                    max-width: 550px;
                }

                .modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px;
                    border-bottom: 1px solid var(--border);
                }

                .modal-header h3 {
                    margin: 0;
                    font-family: 'Fredoka', sans-serif;
                    color: var(--dark);
                    font-size: 18px;
                }

                .modal-close {
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    color: var(--gray);
                }

                .modal-body {
                    padding: 20px;
                }

                .modal-body .form-group {
                    margin-bottom: 16px;
                }

                .modal-body .form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px;
                }

                .modal-body textarea.form-input {
                    min-height: 80px;
                    resize: vertical;
                }

                .modal-footer {
                    display: flex;
                    justify-content: flex-end;
                    gap: 10px;
                    padding: 15px 20px;
                    border-top: 1px solid var(--border);
                    background: #f9fafb;
                    border-radius: 0 0 12px 12px;
                }
            </style>

            <!-- ======================== -->
            <!-- JAVASCRIPT -->
            <!-- ======================== -->
            <script>
                // Modal Functions
                function openModal(id) {
                    document.getElementById(id).style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
                function closeModal(id) {
                    document.getElementById(id).style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
                function openAddUserModal() {
                    openModal('addUserModal');
                }
                function openAddMenuModal() {
                    openModal('addMenuModal');
                }

                // Close modal on outside click
                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    modal.addEventListener('click', function (e) {
                        if (e.target === this) closeModal(this.id);
                    });
                });

                // Edit User - Populate modal from data attributes
                document.querySelectorAll('.edit-user-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.getElementById('edit_user_id').value = this.dataset.id;
                        document.getElementById('edit_user_name').value = this.dataset.name;
                        document.getElementById('edit_user_email').value = this.dataset.email;
                        document.getElementById('edit_user_role').value = this.dataset.role;
                        openModal('editUserModal');
                    });
                });

                // Edit Menu - Populate modal from data attributes
                document.querySelectorAll('.edit-menu-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.getElementById('edit_menu_id').value = this.dataset.id;
                        document.getElementById('edit_menu_name').value = this.dataset.name;
                        document.getElementById('edit_menu_price').value = this.dataset.price;
                        document.getElementById('edit_menu_category').value = this.dataset.category;
                        document.getElementById('edit_menu_description').value = this.dataset.description || '';
                        document.getElementById('edit_menu_alt').value = this.dataset.alt || '';
                        document.getElementById('edit_menu_current_image').textContent = this.dataset.image ? 'Current: ' + this.dataset.image : '';
                        openModal('editMenuModal');
                    });
                });

                // Filter Users Table (JavaScript)
                function filterUsers(role, btn) {
                    // Update active button
                    btn.closest('.filter-group').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Filter rows
                    const rows = document.querySelectorAll('#usersTable tbody tr');
                    rows.forEach(row => {
                        if (role === 'all' || row.dataset.role === role) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                // Search Users Table
                function searchUsers() {
                    const input = document.getElementById('userSearchInput').value.toLowerCase();
                    const rows = document.querySelectorAll('#usersTable tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(input) ? '' : 'none';
                    });
                }

                // Filter Menu Table (JavaScript)
                function filterMenu(category, btn) {
                    // Update active button
                    btn.closest('.filter-group').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Filter rows
                    const rows = document.querySelectorAll('#menuTable tbody tr');
                    rows.forEach(row => {
                        if (category === 'all' || row.dataset.category === category) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                // Search Menu Table
                function searchMenu() {
                    const input = document.getElementById('menuSearchInput').value.toLowerCase();
                    const rows = document.querySelectorAll('#menuTable tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(input) ? '' : 'none';
                    });
                }
            </script>

            <!-- Custom Confirm Modal -->
            <div class="confirm-modal-overlay" id="customConfirmModal">
                <div class="confirm-modal">
                    <div class="confirm-modal-icon">
                        <i class="ph ph-warning-circle"></i>
                    </div>
                    <h3>Are you sure?</h3>
                    <p id="confirmMessage">Do you really want to perform this action?</p>
                    <div class="confirm-modal-actions">
                        <button class="btn-cancel" id="confirmCancelBtn">No, Cancel</button>
                        <button class="btn-confirm" id="confirmYesBtn">Yes, Proceed</button>
                    </div>
                </div>
            </div>

            <script>
                // Custom Confirm Modal Logic
                let confirmCallback = null;

                function showConfirmModal(message, callback) {
                    document.getElementById('confirmMessage').innerText = message;
                    document.getElementById('customConfirmModal').classList.add('show');
                    confirmCallback = callback;
                }

                function closeConfirmModal() {
                    document.getElementById('customConfirmModal').classList.remove('show');
                    confirmCallback = null;
                }

                const confirmYesBtn = document.getElementById('confirmYesBtn');
                if (confirmYesBtn) {
                    confirmYesBtn.addEventListener('click', function () {
                        if (confirmCallback) confirmCallback();
                        closeConfirmModal();
                    });
                }

                const confirmCancelBtn = document.getElementById('confirmCancelBtn');
                if (confirmCancelBtn) {
                    confirmCancelBtn.addEventListener('click', closeConfirmModal);
                }

                // Helper for forms
                function confirmFormSubmit(event, message) {
                    event.preventDefault();
                    const form = event.target;
                    showConfirmModal(message, function () {
                        form.submit();
                    });
                    return false;
                }
                function confirmLink(event, message) {
                    event.preventDefault();
                    const url = event.currentTarget.href;
                    showConfirmModal(message, function () {
                        window.location.href = url;
                    });
                    return false;
                }

                // AJAX Functions for Menu
                function toggleMenuVisibility(btn) {
                    const id = btn.dataset.id;
                    const status = btn.dataset.status;
                    const msg = status == '1' ? 'Hide this item?' : 'Show this item?';

                    showConfirmModal(msg, function () {
                        const formData = new FormData();
                        formData.append('ajax_action', 'toggle_visibility');
                        formData.append('id', id);
                        formData.append('status', status);

                        fetch('dashboard.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update Button State
                                    const newStatus = data.new_status;
                                    btn.dataset.status = newStatus;
                                    const icon = btn.querySelector('i');

                                    if (newStatus == 1) {
                                        icon.className = 'ph ph-eye';
                                        btn.className = 'btn-action btn-action-success';
                                        btn.title = 'Hide Item';
                                    } else {
                                        icon.className = 'ph ph-eye-slash';
                                        btn.className = 'btn-action btn-action-muted';
                                        btn.title = 'Show Item';
                                    }
                                } else {
                                    alert('Error updating status');
                                }
                            })
                            .catch(err => console.error(err));
                    });
                }

                function deleteMenuItem(id, btn) {
                    showConfirmModal('Delete this menu item?', function () {
                        const formData = new FormData();
                        formData.append('ajax_action', 'delete_menu');
                        formData.append('id', id);

                        fetch('dashboard.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove Row
                                    const row = btn.closest('tr');
                                    row.remove();
                                } else {
                                    alert('Error deleting item');
                                }
                            })
                            .catch(err => console.error(err));
                    });
                }
            </script>
</body>

</html>