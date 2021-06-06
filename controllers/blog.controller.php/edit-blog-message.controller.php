<?php
require_once(__DIR__ . "/../../core/routing/controller.core.php");
require_once(__DIR__ . "/../../core/io/file-uploader.core.php");
require_once(__DIR__ . "/../../modules/form-validators/blog-message.validator.php");
require_once(__DIR__ . "/../../views/blog/blog-editor.view.php");
require_once(__DIR__ . "/../../models/blog-message.model.php");

class EditBlogMessageController extends RestController {

    public function GET(Request $request): string {
        return "";
    }

    public function POST(Request $request): string {
        session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== "ADMIN") {
            http_response_code(401);
            return "";
        }

        $inputJSON = json_decode(file_get_contents('php://input'), true);
        header("Content-Type: application/json");

        $postId = $request->getParams()["postId"];
        $validator = new BlogMessageValidator();
        //var_dump($inputJSON);
        $validationResult = $validator->validate($inputJSON);


        if (!$validationResult->isValid()) {
            http_response_code(400);
            return json_encode($validationResult->getErrorMessage());
        }

        $newBlogMessage = BlogMessage::findById($postId);
        $newBlogMessage->setTopic($inputJSON["topic"]);
        $newBlogMessage->setText($inputJSON["text"]);

        if (isset($inputJSON["image"]) && $inputJSON["image"]["size"] !== 0) {
            $uniqueId = uniqid();
            $uploadInfo = FileUploader::uploadFile("image", "../files/userImages/$uniqueId.png");
            if ($uploadInfo->isSuccessful())
                $newBlogMessage->setImagePath($uniqueId);
            else {
                http_response_code(400);
                return json_encode($validationResult->getErrorMessage());
            }
        }
        $savedSuccessfully = $newBlogMessage->save();

        if (!$savedSuccessfully)
            return json_encode($validationResult->getErrorMessage());

        return <<<JSON
            {
                "topic": "{$newBlogMessage->getTopic()}",
                "text": "{$newBlogMessage->getText()}"
            }
        JSON;
    }
}