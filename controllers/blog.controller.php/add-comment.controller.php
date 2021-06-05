<?php
require_once(__DIR__ . "/../../core/routing/controller.core.php");
require_once(__DIR__ . "/../../core/io/file-uploader.core.php");
require_once(__DIR__ . "/../../core/io/file-reader.core.php");
require_once(__DIR__ . "/../../views/blog/upload-blog-messages.view.php");

class AddCommentController extends RestController {

    public function GET(Request $request): string {
        header('Content-type: application/xml');
        return MessageView::render("Файл загружен", "Файл был успешно загружен");
    }

    public function POST(Request $request): string {
        header('Content-type: text/xml');
        [$authenticated] = MessagesController::checkAuthorization();

        if (!$authenticated) {
            http_response_code(401);
            return "<error>Unauthorized</error>";
        }
        $inputXML = file_get_contents('php://input');
        $comment = new SimpleXMLElement($inputXML);

        $message = $comment->message;
        $postId = $comment->postId;

        $newComment = new Comment();
        $newComment->setComment($message);
        $newComment->setPostId((int)$postId);
        $newComment->setUserId((int)$_SESSION['userId']);
        $newComment->save();

        return <<<XML
            <comment>
                <message>{$newComment->getComment()}</message>
                <name>{$newComment->getName()}</name>
                <timestamp>{$newComment->getTimestamp()}</timestamp>
            </comment>
        XML;
    }
}