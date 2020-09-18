<?php
echo password_hash("admin", PASSWORD_DEFAULT);
echo "\n";
echo password_verify('admin', '$2y$10$MrYwRRAGoZPADEwUSHiXpeO0LZsKMDruKnKrb0Dno5xATCtvNHjRG');

?>