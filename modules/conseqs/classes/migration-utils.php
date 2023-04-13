<?php
/**
 * Copyright (C) 2017-2019 Petr Hucik <petr@getdatakick.com>
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the DataKick Regular License version 1.0
 * For more information see LICENSE.txt file
 *
 * @author    Petr Hucik <petr@getdatakick.com>
 * @copyright 2017-2019 Petr Hucik
 * @license   Licensed under the DataKick Regular License version 1.0
 */

namespace Conseqs;

class MigrationUtils
{
    /** @var \Db */
    private $conn;

    /**
     * MigrationUtils constructor.
     * @param \Db $conn
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param $table
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function tableExists($table)
    {
        $table = pSQL($table);
        $q = "SELECT * FROM information_schema.TABLES WHERE table_schema=database() AND table_name = '$table'";
        $res = $this->conn->query($q);
        if ($res && $res->fetch()) {
            return true;
        }
        return false;
    }

    /**
     * @param $table
     * @param $column
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function columnExists($table, $column)
    {
        $column = pSQL($column);
        $q = "SELECT * FROM information_schema.COLUMNS WHERE table_schema = database() AND table_name ='$table' AND column_name = '$column'";
        $res = $this->conn->query($q);
        if ($res && $res->fetch()) {
            return true;
        }
        return false;
    }

}
