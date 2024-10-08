<?php
session_start();
include '../db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login/index.php");
    exit();
}

// Obtener todos los posts junto con el nombre de usuario y la foto de perfil
$stmt = $conn->prepare("
    SELECT p.id, p.post_content, p.post_image, p.created_at, u.username, u.profile_picture, 
           COUNT(c.id) AS comment_count
    FROM posts p
    JOIN usuarios u ON p.username = u.username
    LEFT JOIN comments c ON p.id = c.post_id
    GROUP BY p.id
    ORDER BY p.id DESC
");
$stmt->execute();
$result = $stmt->get_result();
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/stylesAllPost.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>All Posts</title>
</head>

<body>
    <div class="posts-container">
        <?php if (empty($posts)) { ?>
            <p>No posts available.</p>
        <?php } else { ?>
            <?php foreach ($posts as $post) {

                $profile_picture = $post['profile_picture'] ? '../register/uploads/' . basename($post['profile_picture']) : '../img/default-profile.png';
                if (!file_exists($profile_picture)) {
                    $profile_picture = '../img/shifu.png';
                }

                $post_image_path = 'post_images/' . basename($post['post_image']);
            ?>
                <div class="post">
                    <div class="post-header">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="post-profile-picture" />
                        <p class="post-username"><?php echo htmlspecialchars($post['username']); ?></p>
                    </div>
                    <div class="post-content">
                        <?php if (!empty($post['post_image'])) { ?>
                            <img src="<?php echo htmlspecialchars($post_image_path); ?>" alt="Post Image" class="post-image" />
                        <?php } ?>
                        <!-- Contenedor para el texto y la fecha -->
                        <div class="post-text-container">
                            <div class="post-text">
                                <p><?php echo htmlspecialchars($post['post_content']); ?></p>
                            </div>
                        </div>
                        <div class="post-date"><?php echo htmlspecialchars(date("g:i A • m.d.y", strtotime($post['created_at']))); ?></div>



                        <!-- Contenedor los comments-->
                        <div class="post-comments-meta">
                            <a href="dashboard.php">
                                <img src="../img/gif/lainlogogif-unscreen.gif" alt="View All Posts" class="back-profile">
                            </a>

                            <div class="comments-icon" onclick="toggleComments(<?php echo htmlspecialchars($post['id']); ?>)">
                                <span class="material-symbols-outlined">chat_bubble_outline</span>
                                <span><?php echo htmlspecialchars($post['comment_count']); ?></span>
                            </div>
                        </div>
                        <div class="post-comments-container" id="comments-<?php echo htmlspecialchars($post['id']); ?>" style="display: none;">
                            <div class="comments-list"></div>

                            <!-- Agregar los comentarios -->
                            <div class="comment-form">
                                <textarea id="comment-text-<?php echo htmlspecialchars($post['id']); ?>" placeholder="Write your comment..."></textarea>
                                <button onclick="postComment(<?php echo htmlspecialchars($post['id']); ?>)">Post Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <script>
        function toggleComments(postId) {
            const commentsContainer = document.getElementById('comments-' + postId);
            const commentForm = commentsContainer.querySelector('.comment-form');

            if (commentsContainer.style.display === 'block') {
                commentsContainer.style.display = 'none';
            } else {
                fetch(`get_comments.php?post_id=${postId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let commentsHTML = '';
                            data.forEach(comment => {
                                const profilePic = comment.profile_picture ? '../register/' + comment.profile_picture : '../img/default-profile.png';
                                commentsHTML += `
                            <div class="comment">
                                <div class="comment-header">
                                    <img src="${profilePic}" alt="Profile Picture" class="comment-profile-picture" />
                                    <p class="comment-username">${comment.username}</p>
                                </div>
                                <p class="comment-content">${comment.comment_content}</p>
                                <p class="comment-date">${new Date(comment.created_at).toLocaleString()}</p>
                            </div>`;
                            });
                            commentsContainer.querySelector('.comments-list').innerHTML = commentsHTML;
                        } else {
                            commentsContainer.querySelector('.comments-list').innerHTML = '<p>No comments.</p>';
                        }

                        commentsContainer.style.display = 'block';

                    })
                    .catch(error => console.error('Error fetching comments:', error));
            }
        }

        function postComment(postId) {
            const commentText = document.getElementById('comment-text-' + postId).value;

            if (commentText.trim() === '') {
                alert('Comment cannot be empty.');
                return;
            }

            fetch('post_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'post_id': postId,
                        'comment_text': commentText
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        document.getElementById('comment-text-' + postId).value = '';

                        fetch(`get_comments.php?post_id=${postId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length > 0) {
                                    let commentsHTML = '';
                                    data.forEach(comment => {
                                        const profilePic = comment.profile_picture ? '../register/' + comment.profile_picture : '../img/default-profile.png';
                                        commentsHTML += `
                                <div class="comment">
                                    <div class="comment-header">
                                        <img src="${profilePic}" alt="Profile Picture" class="comment-profile-picture" />
                                        <p class="comment-username">${comment.username}</p>
                                    </div>
                                    <p class="comment-content">${comment.comment_content}</p>
                                    <p class="comment-date">${new Date(comment.created_at).toLocaleString()}</p>
                                </div>`;
                                    });
                                    document.querySelector(`#comments-${postId} .comments-list`).innerHTML = commentsHTML;
                                } else {
                                    document.querySelector(`#comments-${postId} .comments-list`).innerHTML = '<p>No comments.</p>';
                                }
                            })
                            .catch(error => console.error('Error fetching comments:', error));
                    } else {
                        alert('Error posting comment: ' + result.message);
                    }
                })
                .catch(error => console.error('Error posting comment:', error));
        }
    </script>
</body>

</html>