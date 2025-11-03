<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Site_setting_model extends CI_Model
{
    private $table = 'site_settings';
    private $primary_key = 'setting_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    public function get_all_as_array()
    {
        $settings = $this->get_all();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->setting_key] = $setting->setting_value;
        }
        return $result;
    }

    public function get_by_key($key)
    {
        return $this->db->get_where($this->table, ['setting_key' => $key])->row();
    }

    public function get_value($key, $default = null)
    {
        $setting = $this->get_by_key($key);
        return $setting ? $setting->setting_value : $default;
    }

    public function update_by_key($key, $value)
    {
        $this->db->where('setting_key', $key);
        $existing = $this->db->get($this->table)->row();

        if ($existing) {
            $this->db->where('setting_key', $key);
            return $this->db->update($this->table, ['setting_value' => $value]);
        } else {
            return $this->db->insert($this->table, [
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    }

    public function update_multiple($settings)
    {
        $this->db->trans_start();
        foreach ($settings as $key => $value) {
            $this->update_by_key($key, $value);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
