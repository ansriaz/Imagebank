<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Logic\Services\DBService;

use Log;

class WelcomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DBService $db)
    {
    	$this->db = $db;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mytime = date('Y_m_d_H_i_s');
        session()->put('starttime',$mytime); 
        Log::info(session()->get('starttime'));

        return view('home');
    }

    public function GetReport()
    {
        Log::info('===================HOME====================');
        $options = array(
            'images' => array('total'=>$this->db->getImagesCount(),
                                'user'=>$this->db->getUserImagesCount(),
                                'system'=>$this->db->getAdminImagesCount()),
            'classes' => array('total'=>$this->db->getClassesCount(),
                                'user'=>$this->db->getUserClasslabelsCount(),
                                'system'=>$this->db->getAdminClasslabelsCount()),
            'datasets' => array('total'=>$this->db->getDatasetsCount(),
                                'user'=>$this->db->getUserDatasetCount(),
                                'system'=>$this->db->getAdminDatasetCount())
        );

        Log::info('[REPORT] ' . json_encode($options));
        return $options;
    }

}
