<?php


namespace Twitter;

/**
 * Class Auth
 * @package Twitter
 */
Class Auth {


    /**
     * Check user login against login form data
     *
     * @param Application $app
     * @param $request
     * @return array
     */
    public function checkAuth(Application $app, $request)
    {
        $qry = $app["pdo"]->prepare("SELECT userid,handle FROM users WHERE handle = ?  AND password = ?");

        $qry->Execute(array($request->get('username'), sha1($request->get('password'))));


        return $qry->fetch(\PDO::FETCH_ASSOC);
    }
}