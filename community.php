<?php
// community.php
session_start();
include "config.php";

// Bắt buộc phải đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// XỬ LÝ FORM
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_post') {
        $content = trim($_POST['post_content'] ?? '');
        if ($content === '') {
            $errors[] = "Nội dung bài viết không được để trống.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO post (content, created_at, user_id) VALUES (:content, NOW(), :user_id)");
                $stmt->execute([
                    ':content' => $content,
                    ':user_id' => $user_id
                ]);
                $new_post_id = $pdo->lastInsertId();

                $stmtUsers = $pdo->prepare("SELECT user_id FROM users WHERE user_id <> :me");
                $stmtUsers->execute([':me' => $user_id]);
                $others = $stmtUsers->fetchAll(PDO::FETCH_COLUMN);

                if ($others) {
                    $noti_content = htmlspecialchars($username, ENT_QUOTES) . " đã đăng một bài mới.";
                    $insNoti = $pdo->prepare("INSERT INTO notifications (content, sent_at, user_id) VALUES (:content, NOW(), :uid)");
                    foreach ($others as $uid) {
                        $insNoti->execute([':content' => $noti_content, ':uid' => $uid]);
                    }
                }

                header("Location: community.php");
                exit();
            } catch (PDOException $e) {
                $errors[] = "Lỗi khi tạo bài: " . $e->getMessage();
            }
        }
    }

    if ($action === 'create_comment') {
        $post_id = intval($_POST['post_id'] ?? 0);
        $cmt_content = trim($_POST['comment_content'] ?? '');

        if ($post_id <= 0 || $cmt_content === '') {
            $errors[] = "Bình luận không hợp lệ hoặc trống.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO comment (content_cmt, created_cmt, post_id, user_id)
                                       VALUES (:content, NOW(), :post_id, :user_id)");
                $stmt->execute([
                    ':content' => $cmt_content,
                    ':post_id' => $post_id,
                    ':user_id' => $user_id
                ]);

                $stmtOwner = $pdo->prepare("SELECT user_id FROM post WHERE post_id = :pid");
                $stmtOwner->execute([':pid' => $post_id]);
                $owner = $stmtOwner->fetchColumn();

                if ($owner && $owner != $user_id) {
                    $noti_content = htmlspecialchars($username, ENT_QUOTES) . " đã bình luận vào bài của bạn.";
                    $insNoti = $pdo->prepare("INSERT INTO notifications (content, sent_at, user_id)
                                              VALUES (:content, NOW(), :uid)");
                    $insNoti->execute([':content' => $noti_content, ':uid' => $owner]);
                }

                header("Location: community.php");
                exit();

            } catch (PDOException $e) {
                $errors[] = "Lỗi khi thêm bình luận: " . $e->getMessage();
            }
        }
    }
}

// LẤY DỮ LIỆU
try {
    $sql = "SELECT p.post_id, p.content, p.created_at, p.user_id, u.username 
            FROM post p
            JOIN users u ON p.user_id = u.user_id
            ORDER BY p.created_at DESC";
    $posts = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $stmtC = $pdo->query("
        SELECT c.cmt_id, c.content_cmt, c.created_cmt, c.post_id, c.user_id, u.username
        FROM comment c
        JOIN users u ON c.user_id = u.user_id
        ORDER BY c.created_cmt ASC
    ");
    $commentsAll = $stmtC->fetchAll(PDO::FETCH_ASSOC);

    $commentsByPost = [];
    foreach ($commentsAll as $c) {
        $commentsByPost[$c['post_id']][] = $c;
    }

} catch (PDOException $e) {
    $errors[] = "Lỗi khi tải dữ liệu: " . $e->getMessage();
}

function esc($s) {
    return nl2br(htmlspecialchars($s, ENT_QUOTES, 'UTF-8'));
}
?>

<!doctype html>
<html lang="vi">
<?php include "navbar.php"; ?>

<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)">
<div class="max-w-4xl mx-auto py-10 px-4">

   <div class="mb-6 text-center">
   

    <h1 class="text-2xl font-bold text-white leading-loose">
        Cộng Đồng Habitu 🐱
    </h1>

    <p class="text-sm text-white leading-relaxed">
        Chia sẻ hành trình và động viên nhau 🌟
    </p>
</div>

    <?php if ($errors): ?>
        <div class="mb-4">
            <?php foreach ($errors as $err): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-2"><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form đăng bài -->
    <div class="bg-white rounded-2xl shadow p-5 mb-8 border border-teal-100">
        <form method="post" action="community.php">
            <input type="hidden" name="action" value="create_post">
            <textarea name="post_content" rows="4" class="w-full p-3 border rounded-lg"
                      placeholder="Chia sẻ câu chuyện của bạn..."></textarea>

            <div class="flex items-center justify-between mt-3">
                <div class="text-sm text-gray-500">
                    Đăng với tư cách: <span class="font-semibold text-teal-700"><?= htmlspecialchars($username) ?></span>
                </div>
                <button class="bg-teal-400 hover:bg-teal-500 text-white px-4 py-2 rounded-full flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Đăng Bài
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách bài -->
    <div class="space-y-6">
        <?php foreach ($posts as $p): ?>
        <div class="bg-white rounded-2xl shadow p-5 border border-teal-50">

            <div class="flex items-start gap-4">

                <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-xl text-teal-700 font-bold">
                    <?= strtoupper(substr($p['username'],0,1)) ?>
                </div>

                <div class="flex-1">

                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold text-teal-700"><?= htmlspecialchars($p['username']) ?></div>
                            <div class="text-xs text-gray-400"><?= date('H:i d/m/Y', strtotime($p['created_at'])) ?></div>
                        </div>
                    </div>

                    <div class="mt-4 text-gray-800"><?= esc($p['content']) ?></div>

                    <div class="mt-4 border-t pt-3 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-4 text-gray-500">

                            

                            <!-- ⭐ ICON BÌNH LUẬN → MỞ COMMENT -->
                            <div class="flex items-center gap-2 cursor-pointer"
                                 onclick="toggleComments(<?= $p['post_id'] ?>)">
                                <i class="far fa-comment"></i>
                                <span><?= isset($commentsByPost[$p['post_id']]) ? count($commentsByPost[$p['post_id']]) : 0 ?></span>
                            </div>

                        </div>

                        <div class="text-xs text-gray-400">ID: <?= $p['post_id'] ?></div>
                    </div>

                    <!-- ⭐ VÙNG COMMENT (HIDDEN DEFAULT) -->
                    <div id="comments-<?= $p['post_id'] ?>" class="mt-4 space-y-3 hidden">

                        <!-- Danh sách comment -->
                        <?php if (!empty($commentsByPost[$p['post_id']])): ?>
                            <?php foreach ($commentsByPost[$p['post_id']] as $c): ?>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-semibold text-sm">
                                            <?= strtoupper(substr($c['username'],0,1)) ?>
                                        </div>
                                        <div class="flex-1 text-sm">
                                            <div class="font-medium">
                                                <?= htmlspecialchars($c['username']) ?>
                                                <span class="text-gray-400 text-xs ml-2">
                                                    <?= date('H:i d/m/Y', strtotime($c['created_cmt'])) ?>
                                                </span>
                                            </div>
                                            <div class="text-gray-700 mt-1">
                                                <?= nl2br(htmlspecialchars($c['content_cmt'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Form bình luận -->
                        <form method="post" action="community.php" class="flex gap-3 items-start mt-2">
                            <input type="hidden" name="action" value="create_comment">
                            <input type="hidden" name="post_id" value="<?= intval($p['post_id']) ?>">
                            <textarea name="comment_content" rows="1"
                                      class="flex-1 p-2 border rounded-lg"
                                      placeholder="Viết bình luận..."></textarea>
                            <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-full">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>

                    </div> <!-- /comments wrapper -->

                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

</div>

<?php include "footer.php"; ?>

<script>
function toggleComments(id) {
    document.getElementById("comments-" + id).classList.toggle("hidden");
}
</script>

</body>
</html>
