<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Migration $migration
 */

class Migrate extends CI_Controller
{
    public function index()
    {
        $this->load->library('migration');
        if ( ! $this->migration->latest())
        {
            show_error($this->migration->error_string());
        }
        else
        {
            echo "Database migrated successfully!";
        }
    }
}
