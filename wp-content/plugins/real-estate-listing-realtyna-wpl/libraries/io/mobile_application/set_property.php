<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_io_cmd_set_property extends wpl_io_cmd_base
{
    private $built;
    private $pid;
    private $property_uid;

    /**
     * This method is the main method of each commands
     * @return mixed
     */
    public function build()
    {
        $this->property_uid = $this->params['user_id'];
        $this->pid = wpl_property::create_property_default($this->property_uid);
        $files = wpl_request::get('files');
        $image_index = 0;

        $data = array();
        foreach ($this->params as $key => $value)
        {
            if(stripos($key, 'field_') === 0)
            {
                $field = substr($key, 6);
                $data[$field] = str_replace('%20', ' ', $value);
            }
            elseif (stripos($key, 'image_') === 0)
            {
                $name = substr($key, 6);
                $image = base64_decode($value);
                $this->save_image($name, $image, $image_index);
                $image_index++;
            }
        }

        foreach ($files['file']['name'] as $key => $file)
        {
            if (stripos($file, 'image_') === 0)
            {
                $name = substr($file, 6);
                $image = $files['file']['tmp_name'][$key];
                $this->save_image($name, $image, $image_index, false);
                $image_index++;
            }
        }

        $this->update_property($data);
        $this->built['result'] = array('success' => true, 'message' => '');
        return $this->built;
    }

    /**
     * Data validation
     * @return boolean
     */
    public function validate()
    {
        return true;
    }

    /**
     * Update property
     * @author Steve A. <steve@realtyna.com>
     * @param  array   $data Input Data
     * @return null
     */
    private function update_property($data)
    {
        if(!$this->pid or !$data) return false;

        $q = '';
        foreach($data as $column => $value) $q .= "`$column` = '".wpl_db::escape($value)."',";
        $q = trim($q, ',');

        wpl_db::q("UPDATE `#__wpl_properties` SET $q WHERE `id` = '{$this->pid}'", 'update');
        wpl_locations::update_LatLng(NULL, $this->pid);
        wpl_property::finalize($this->pid, 'edit', $this->property_uid);

        return true;
    }

    /**
     * Save property image
     * @author Steve A. <steve@realtyna.com>
     * @param  string  $name  Image Name
     * @param  string  $image Image Data or Path
     * @param  integer $index Image Index
     * @param  boolean  $data  Is Image Data?
     * @return void
     */
    private function save_image($name, $image, $index = 0, $data = true)
    {
        $kind = 0; // We support properties only, for now
        $item_type = 'gallery'; // For efficiency
        $item_cat = 'image'; // For efficiency
        if($data) $name .= ".jpg"; // All images are stored in JPEG format if the image data is sent

        $blog_id = wpl_property::get_blog_id($this->pid);
        $path = wpl_global::get_upload_base_path($blog_id).$this->pid.DS.$name;

        if($data)
        {
            wpl_file::write($path, $image);
        }
        else
        {
            wpl_file::upload($image, $path);
        }

        $item = array('parent_id' => $this->pid, 'parent_kind' => $kind, 'item_type' => $item_type, 'item_cat' => $item_cat, 'item_name' => $name, 'creation_date' => date("Y-m-d H:i:s"), 'index' => $index);
        wpl_items::save($item);
    }
}