<?php

namespace System\Database;

use Exception;
use System\File\File;
use PDO;
use PDOException;
use System\Http\Request;
use System\Url\Url;

class Database
{
    /**
     * 
     */
    protected static $instance;
    /**
     * 
     */
    protected static $connection;
    /**
     * 
     */
    protected static $select;
    /**
     * 
     */
    protected static $table;
    /**
     * 
     */
    protected static $join;
    /**
     * 
     */
    protected static $where;
    /**
     * 
     */
    protected static $group_by;
    /**
     * 
     */
    protected static $having;
    /**
     * 
     */
    protected static $where_binding     = [];
    /**
     * 
     */
    protected static $having_binding    = [];
    /**
     * 
     */
    protected static $binding           = [];
    /**
     * 
     */
    protected static $order_by;
    /**
     * 
     */
    protected static $limit;
    /**
     * 
     */
    protected static $offset;
    /**
     * 
     */
    protected static $query;
    /**
     * 
     */
    protected static $satter;
    /**
     * 
     */
    private function __construct()
    {
    }
    /**
     * 
     */
    private static function connect()
    {
        if (!static::$connection) {
            $database_config = File::getFile("config/database.php");
            $dsn    = "mysql:dbname=" . $database_config['DB_NAME'] . ";host=" . $database_config['DB_HOST'] . "";
            $option = [
                PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT            => false,
                PDO::MYSQL_ATTR_INIT_COMMAND    => 'set NAMES ' . $database_config['DB_CHARSET'] . ' COLLATE ' . $database_config['DB_COLL'],
            ];
            try {
                static::$connection = new PDO($dsn, $database_config['DB_USER'], $database_config['DB_PASS'], $option);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
    /**
     * 
     */
    private static function instance()
    {
        static::connect();
        if (!static::$instance) {
            static::$instance = new Database();
        }
        return static::$instance;
    }
    /**
     * 
     */
    public static function query($query = null)
    {
        static::instance();
        if ($query == null) {
            if (!static::$table) {
                throw new Exception("unknow Table " . static::$table);
            }
            $query = "SELECT ";
            $query .= static::$select ?: ' * ';
            $query .= ' FROM ' . static::$table . ' ';
            $query .= static::$join . ' ';
            $query .= static::$where . ' ';
            $query .= static::$group_by . ' ';
            $query .= static::$having . ' ';
            $query .= static::$order_by . ' ';
            $query .= static::$limit . ' ';
            $query .= static::$offset . ' ';
        }
        static::$query = $query;
        static::$binding = array_merge(static::$where_binding, static::$having_binding);
        return static::instance();
    }
    /**
     * 
     */
    public static function select()
    {
        $select = func_get_args();
        $select = implode(', ', $select);
        static::$select = $select;
        return static::instance();
    }
    /**
     * 
     */
    public static function table($table)
    {
        static::$table = $table;
        return static::instance();
    }
    /**
     * 
     */
    public static function where($colum, $operator, $value, $type = null)
    {
        $where = ' `' . $colum . '` ' . $operator . ' ? ';
        if (!static::$where) {
            $statement = " WHERE " . $where;
        } else {
            if ($type === null) {
                $statement = " AND " . $where;
            } else {
                $statement = " " . $type . " " . $where;
            }
        }
        static::$where .= $statement;
        static::$where_binding[] = htmlspecialchars($value);
        return static::instance();
    }
    /**
     * 
     */
    public static function or_where($colum, $operator, $value)
    {
        static::where($colum, $operator, $value, "OR");
        return static::instance();
    }
    /**
     * 
     */
    public static function group_by()
    {
        $group_by = func_get_args();
        $group_by = " GROUP BY " . implode(', ', $group_by) . " ";
        static::$group_by = $group_by;
        return static::instance();
    }
    /**
     * 
     */
    public static function order_by($colum, $type = null)
    {
        $sep    = static::$order_by ? ' , ' : ' ORDER BY ';
        $type   = strtoupper($type);
        $type   = ($type !== null && in_array($type, ['ASC', 'DESC'])) ? $type : ' ASC ';
        $statement = $sep . $colum . " " . $type . " ";
        static::$order_by .= $statement;
        return static::instance();
    }
    /**
     * 
     */
    public static function join($table, $first, $operator, $sacand, $type = 'INNER')
    {
        static::$join .= " " . $type . " JOIN " . $table . " ON " . $first . $operator . $sacand . " ";
        return static::instance();
    }
    /**
     * 
     */
    public static function left_join($table, $first, $operator, $sacand)
    {
        static::join($table, $first, $operator, $sacand, 'LEFT');
        return static::instance();
    }
    /**
     * 
     */
    public static function right_join($table, $first, $operator, $sacand)
    {
        static::join($table, $first, $operator, $sacand, 'RIGHT');
        return static::instance();
    }
    /**
     * 
     */
    public static function having($colum, $operator, $value)
    {
        $having = ' `' . $colum . '` ' . $operator . ' ? ';
        if (!static::$having) {
            $statement = " HAVING " . $having;
        } else {
            $statement = " AND " . $having;
        }
        static::$having .= $statement;
        static::$having_binding[] = htmlspecialchars($value);
        return static::instance();
    }
    /**
     * 
     */
    public static function limit($limit)
    {
        static::$limit = " LIMIT " . $limit . " ";
        return static::instance();
    }
    /**
     * 
     */
    public static function offset($offset)
    {
        static::$offset = " OFFSET " . $offset . " ";
        return static::instance();
    }
    /**
     * 
     */
    private static function fetch_execute()
    {
        static::query(static::$query);
        $query = trim(static::$query, " ");
        $data  = static::$connection->prepare($query);
        $data->execute(static::$binding);
        static::clear();
        return $data;
    }
    /**
     * 
     */
    public static function get()
    {
        $data = static::fetch_execute();
        $result = $data->fetchAll();
        return $result;
    }
    /**
     * 
     */
    public static function first()
    {
        $data = static::fetch_execute();
        $result = $data->fetch();
        return $result;
    }
    /**
     * 
     */
    private static function executeing(array $data, $query, $where = null)
    {
        static::instance();
        if (!static::$table) {
            throw new Exception("Unknow Table");
        }


        foreach ($data as $key => $value) {
            static::$satter .= " `" . $key . "` = ? , ";
            static::$binding[] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        static::$satter = trim(static::$satter, ', ');
        $query .= static::$satter;
        $query .= $where !== null ? static::$where . " " : " ";
        static::$binding = $where !== null ? array_merge(static::$binding, static::$where_binding) : static::$binding;

        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);
        static::clear();
    }
    /**
     * 
     */
    public static function insert($data)
    {
        $table = static::$table;
        $query = "INSERT INTO " . $table . " SET ";
        static::executeing($data, $query);
        $objID = static::$connection->lastInsertId();
        return $objID;
    }
    /**
     * 
     */
    public static function update($data)
    {
        $query = "UPDATE " . static::$table . " SET ";
        static::executeing($data, $query, true);
        return true;
    }
    /**
     * 
     */
    public static function delete()
    {
        $query = "DELETE FROM " . static::$table . " ";
        static::executeing([], $query, true);
        return true;
    }
    /**
     * 
     */
    public static function pagination($per_page = 15)
    {
        static::query(static::$query);
        $query = trim(static::$query, " ");
        $data  = static::$connection->prepare($query);
        $data->execute();
        $pages = ceil($data->rowCount() / $per_page);
        $page = Request::_GET("page");
        $current_page = (!is_numeric($page) || Request::_GET("page") < 1) ? "1" : $page;
        $offset = ($current_page - 1) * $per_page;
        static::limit($per_page);
        static::offset($offset);
        static::query();
        $data = static::fetch_execute();
        $result = $data->fetchAll();
        $res = [
            'data'          => $result,
            'per_page'      => $per_page,
            'pages'         => $pages,
            'current_page'  => $current_page
        ];
        return $res;
    }
    /**
     * 
     */
    public static function pagination_links($current_page, $pages)
    {
        $links  = "";
        $from   = $current_page - 2;
        $to     = $current_page + 2;
        if ($from < 2) {
            $from = 2;
            $to = $from + 4;
        }
        if ($to >= $pages) {
            $diff = $to - $pages + 1;
            $from = ($from > 2) ? $from - $diff : 2;
            $to = $pages - 1;
        }
        if ($from < 2) {
            $from = 1;
        }
        if ($to >= $pages) {
            $to = ($pages - 1);
        }
        if ($pages > 1) {
            $links .= "<ul class=''>";

            $full_link = Url::path(Request::getFullURL());
            $full_link = preg_replace('/\?page=(.*)/', '', $full_link);
            $full_link = preg_replace('/\&page=(.*)/', '', $full_link);

            $current_active = $current_page == 1 ? ' active ' : '';
            $href = strpos($full_link, "?") ? ($full_link . '&page=1') : ($full_link . '?page=1');
            $links .= "<li class='link" . $current_active . "'><a href='" . $href . "'>First</a></li>";

            for ($i = $from; $i <= $to; $i++) {
                $current_active = $current_page == $i ? ' active ' : '';
                $href = strpos($full_link, "?") ? ($full_link . '&page=' . $i) : ($full_link . '?page=' . $i);
                $links .= "<li class='link" . $current_active . "'><a href='" . $href . "'>" . $i . "</a></li>";
            }

            if ($pages > 1) {
                $current_active = $current_page == $pages ? ' active ' : '';
                $href = strpos($full_link, "?") ? /*($full_link . '&page=' . $pages)*/: ($full_link . '?page=' . $pages);
                $links .= "<li class='link" . $current_active . "'><a href='" . $href . "'>Last</a></li>";
            }
            $links .= "</ul>";
            return $links;
        }
    }
    /**
     * 
     */
    private static function clear()
    {
        static::$select = "";
        static::$join = "";
        static::$where = "";
        static::$where_binding = [];
        static::$order_by = "";
        static::$having = "";
        static::$having_binding = [];
        static::$group_by = "";
        static::$limit = "";
        static::$offset = "";
        static::$query = "";
        static::$binding = [];
        static::$instance = "";
        static::$satter = "";
    }
}
