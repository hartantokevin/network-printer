<?php
defined('BASEPATH') or exit('No direct script access allowed');
class sse extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/userguide3/general/urls.html
     */
    public function index()
    {
        $this->load->view('page_index');
    }

    public function detail()
    {
        echo json_encode($this->db->get('work_orders')->result());
    }

    public function update($id)
    {
        $this->db->trans_begin();
        $this->db->where('id', $id);
        unset($_POST['id']);
        $execQuery = $this->db->update('work_orders', $_POST);
        if ($execQuery) {
            if ($this->db->affected_rows() == 1) {
                $this->db->trans_commit();
                echo json_encode(["msg" => "Update Successfully", "status" => "200"]);
            } else if ($this->db->affected_rows() > 1) {
                $this->db->trans_rollback();
                echo json_encode(["msg" => "Please contact IT", "status" => "403"]);
            }
        } else
            echo json_encode(["msg" => "Update Failed", "status" => "500"]);
    }

    public function event_data()
    {
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");

        $initData = json_encode($this->db->get('work_orders')->result());
        while (1) {
            // data
            echo "event: ping\n";
            echo 'data: ' . $initData;
            echo "\n\n";

            $currData = json_encode($this->db->get('work_orders')->result());
            if ($currData != $initData) {
                echo 'data: ' . $currData, "\n\n";
                $initData = $currData;
            }

            while (ob_get_level() > 0)
                ob_end_flush();
            flush();

            if (connection_aborted())
                break;

            // sleep for 1 second before running the loop again
            sleep(1);
        }
    }
}
