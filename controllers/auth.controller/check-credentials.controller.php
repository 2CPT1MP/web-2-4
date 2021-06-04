<?php


class UniqueCredentialsResponse {
    public bool $uniqueLogin;
    public bool $uniqueEmail;
}

class CheckCredentialsController extends RestController {

    public function GET(Request $request): string {
        return "";
    }

    public function POST(Request $request): string {
        header("Content-Type: application/json");
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);

        $login = "";
        $email = "";

        if (isset($input["login"]))
            $login = $input["login"];
        if (isset($input["email"]))
            $email = $input["email"];

        $loginFilter = new Filter();
        $loginFilter->addCondition('login', $login);

        $emailFilter = new Filter();
        $emailFilter->addCondition('email', $email);

        $response = new UniqueCredentialsResponse();

        if ($login)
            $response->uniqueLogin = count(User::find($loginFilter)) === 0;
        if ($email)
            $response->uniqueEmail= count(User::find($emailFilter)) === 0;

        $adminFile = file(__DIR__ . '/../../files/pswd.inc');
        if (trim($adminFile[0]) === $login)
            $response->uniqueLogin = false;

        return json_encode($response);
    }
}