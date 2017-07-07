<?php

namespace Twitter;

use Silex\Application as SilexApplication;

/**
 * Class Application
 * @package Twitter
 */
class Application extends SilexApplication
{
    /**
     * gets user's list of tweets
     *
     * @param $userid
     * @return array
     */
    public function getPosts($userid)
    {
        $qry = $this["pdo"]->prepare(
            "SELECT p.id, p.userid, handle, post, stamp FROM posts p
             LEFT JOIN users u ON u.userid = p.userid
              WHERE u.userid = ?
              ORDER BY stamp DESC");

        $qry->Execute(array($userid));

        return $qry->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Saves a tweet
     *
     * @param $post
     * @param $userid
     * @return mixed
     */
    public function savePost($post, $userid)
    {
        $qry = $this["pdo"]->prepare("INSERT INTO posts SET userid = ? , post = ? , stamp = ?");

        return $qry->Execute(array($userid, $post, time()));
    }
}