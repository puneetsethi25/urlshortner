
<?php

class Shortener
{

    private $server = '';
    private $headers = 'Content-Type: application/json';
    private $connection = null;

    private $fromNum = 10;
    private $toNum = 36;

    private $shortUrl;

    public function __construct($server, $connection)
    {
        $this->server = $server;
        $this->connection = $connection;
    }

    private function encode($id)
    {
        return base_convert($id, $this->fromNum, $this->toNum);
    }

    private function decode($id)
    {
        return base_convert($id, $this->toNum, $this->fromNum);
    }

    public function execute()
    {
        if (isset($_GET['url'])) {
            $url = $_GET['url'];
            if (preg_match('/^http[s]?\:\/\/[\w]+/', $url)) {
                $result = $this->find($url);
                if (empty($result)) {
                    $id = $this->save($url);
                    $this->shortUrl = $this->server . '/' . $this->encode($id);
                } else {
                    $this->shortUrl = $this->server . '/' . $this->encode($result['id']);
                }
                $this->sendResponse();
            } else {
                $this->sendErrorResponse();
            }
        } else if (isset($_GET['q'])) {
            $q = $_GET['q'];
            if (strstr($q, '/')) {
                $link_array = explode('/', $q);
                $this->normalRedirect(end($link_array));
            } else {
                $this->normalRedirect($q);
            }
        }

    }

    public function pageNotFound()
    {
        header('Status: 404 Not Found');
        exit('<h1>404 Not Found</h1>' . str_repeat(' ', 512));
    }

    public function fetch($id)
    {
        $statement = $this->connection->prepare('SELECT * FROM urls WHERE id = ?');
        $statement->execute(array($id));
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function normalRedirect($path = false)
    {
        if (!$path) {
            $this->pageNotFound();
        }
        $id = $this->decode($path);
        $result = $this->fetch($id);
        if (!empty($result)) {
            header("Location: " . $result['url'], true, 301);
            exit();
        } else {
            $this->pageNotFound();
        }

    }
    private function sendResponse()
    {
        header($this->headers);
        echo json_encode(array('url' => $this->shortUrl));
    }

    private function sendErrorResponse()
    {
        header($this->headers);
        http_response_code(400);
        echo json_encode(array('error' => "Invalid request parametrer"));

    }

    public function save($url)
    {
        $statement = $this->connection->prepare('INSERT INTO urls (url, created) VALUES (?,?)');
        $statement->execute(array($url, date('Y-m-d H:i:s')));
        return $this->connection->lastInsertId();
    }
    public function find($url)
    {
        $statement = $this->connection->prepare('SELECT * FROM urls WHERE url = ?');
        $statement->execute(array($url));
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

}