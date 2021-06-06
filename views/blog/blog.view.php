<?php use JetBrains\PhpStorm\Pure;

require_once(__DIR__ . '/../header.view.php');

class BlogView {
    /**@param BlogMessage[] $messages  */
    public static function render(array $messages, int $pageCount, int $currentPage): string {
        $html = HeaderView::render('Редактор блога');
        $html .= '<section class="card">';

        $msgs = "<h2>Оставленные сообщения</h2><article class=''>";
        $msgs .= "<b><a href='/blog/editor'>Добавить запись</b><br>";
        $msgs .= "<b><a href='/blog/messages'>Загрузить список записей</a></b>";
        $pageInfo = "<table><tr><td>Страница</td>";

        for ($i = 1; $i <= $pageCount; $i++) {
            if ($currentPage === $i)
                $pageInfo .= "<td><b><a href=\"/blog?page=$i\">$i</a></b></td>";
            else
                $pageInfo .= "<td><a href=\"/blog?page=$i\">$i</a></td>";
        }
        $pageInfo .= "</tr></table>";
        $msgs .= $pageInfo;

        foreach ($messages as $message) {
            $hasImg = $message->hasImage();
            $imagePath = ($hasImg)? $message->getImagePath() : "";

            $form = "";
            if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] != 'ADMIN') {
                $form = "<form action='' class='small message-form'>
                        <input type='hidden' name='postId' value=\"{$message->getId()}\">
                        <input type='text' placeholder='Добавить коментарий' name='message' required>
                        <button type='submit' id='send-message'>Отправить</button>
                    </form>";
            }

            $msgs .= "
               <div class='msg-block' id=\"{$message->getId()}\">
                    <img src='/blog/userImage?id=$imagePath' width='50px' alt='Нет изображения'><br>
                    <p class='no-margin'>{$message->getTimestamp()}</p>
                    <b>{$message->getTopic()}</b><br>
                    <p class='no-margin'>{$message->getText()}</p>
                    {$form}
               </div>
               <script src='/scripts/requests/xhrXmlScript.js' type='module'></script>
            ";

            $msgs .= "<div class='messages-container' id=\"messages-container-{$message->getId()}\">";
            foreach ($message->getComments() as $comment)
                $msgs .= "{$comment->getTimestamp()} <b>{$comment->getName()}</b> {$comment->getComment()}<br>";
            $msgs .= "</div>";
        }

        $msgs .= "</article>";

        return $html . <<<EDITOR
            $msgs
            $pageInfo
        EDITOR;
    }
}