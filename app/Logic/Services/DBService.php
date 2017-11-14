<?php

namespace App\Logic\Services;

use Auth;
use App\Models\Image;
use App\Models\Classlabel;
use App\Models\UserDatasets;
use App\Models\ImageClasslabels;

use Log;

class DBService
{

    /**
     * Get all images
     */
    public function getAllImages()
    {
        $images = Image::all();
        return $images;
    }

    /**
     * Get count of all images
     */
    public function getImagesCount()
    {
        $count = Image::count();
        return $count;
    }

    /**
     * Get count of all user dataset
     */
    public function getUserImagesCount()
    {
        // $count = Image::join('user_datasets','user_datasets.id','=','images.dataset_id')
        //                     -> where('user_datasets.user_id','!=','1')->count();

        $count = ImageClasslabels::join('classlabel','classlabel.id','=','imageclasslabels.class_id')
                            -> where('classlabel.dataset_id','!=','1')
                            -> count();
        return $count;
    }

    /**
     * Get count of all system datasets downloaded by crawler
     */
    public function getAdminImagesCount()
    {
        // $count = Image::join('user_datasets','user_datasets.id','=','images.dataset_id')
        //                     -> where('user_datasets.user_id','=','1')->count();

        $count = ImageClasslabels::join('classlabel','classlabel.id','=','imageclasslabels.class_id')
                            -> where('classlabel.dataset_id','=','1')
                            -> count();
        return $count;
    }

    /**
     * Get count of all user dataset
     */
    public function getUserDatasetCount()
    {
        $count = UserDatasets::where('user_id','!=','1')->count();
        return $count;
    }

    /**
     * Get count of all system datasets downloaded by crawler
     */
    public function getAdminDatasetCount()
    {
        $count = UserDatasets::where('user_id','=','1')->count();
        return $count;
    }

    /**
     * Get count of all classlabels created by crawler
     */
    public function getAdminClasslabelsCount()
    {
        $count = Classlabel::join('user_datasets','user_datasets.id','=','classlabel.dataset_id')
                            -> where('user_datasets.user_id','=','1')->count();
        return $count;
    }

    /**
     * Get count of all classlabels created by users
     */
    public function getUserClasslabelsCount()
    {
        $count = Classlabel::join('user_datasets','user_datasets.id','=','classlabel.dataset_id')
                            -> where('user_datasets.user_id','!=','1')->count();
        return $count;
    }

    /**
     * Get image by id, name or filename
     */
    public function getImageByIdNameOrFilename( $id, $name, $filename )
    {
        $image = Image::where('id', '=', $id)
                 ->orWhere('name','=',$name)
                 ->orWhere('filename','=',$filename)->first();

        return $image;
    }

    /**
     * Search iamges by tags
     */
    public function searchImageByTags( $tag )
    {
        $images = Image::where('tags', 'like', '%'.$tag.'%')->get();

        return $images;
    }

    /**
     * Search images by classlabesl
     */
    public function searchImagesByClasslabels( $query )
    {
        $label = Classlabel::where('title', 'like', $query)->first();
        $image_ids = ImageClasslabels::where('class_id','=',$label->id)->get();
        $images = array();
        foreach ($image_ids as $obj) {
            $image = $this->getImageByIdNameOrFilename($obj->image_id,null,null);
            array_push($images, $image);
        }

        return $images;
    }

    /**
     * Get datasets count
     */
    public function getDatasetsCount()
    {
        $count = UserDatasets::all()->count();
        return $count;
    }

    /**
     * Get datasets
     */
    public function getAllDatasets()
    {
        $count = UserDatasets::all();
        return $count;
    }

    /**
     * Get all labels
     */
    public function getAllClasses()
    {
        $labels = Classlabel::all();
        return $labels;
    }

    /**
     * Get class label count
     */
    public function getClassesCount()
    {
        $count = Classlabel::all()->count();
        return $count;
    }

    public function getClasslabelById($label)
    {
        $lbl = Classlabel::where('title', '=', $label)->first();
        return $lbl;
    }

    public function getClasslabelsByDatasetId($datasetId)
    {
        $lbl = Classlabel::where('dataset_id', '=', $datasetId)->get();
        return $lbl;
    }

    public function getClasslabelsByDataset($dataset_title)
    {
        $dataset_id = UserDatasets::where('title','=',$dataset_title)->first();
        // Log::info($dataset_id);
        $lbls = Classlabel::where('dataset_id', '=', $dataset_id->id)->get();
        Log::info($lbls);
        return $lbls;
    }

    public function getUserDatasetByName($title)
    {
        $obj = UserDatasets::where('title', 'like', $title)->first();
        return $obj;
    }

    /**
     * Get all labels of specific image
     */
    public function getAllClassesOfImage( $image_id )
    {
        $labels = Classlabel::where('image_id','=',$image_id)->get();
        return $labels;
    }

    /**
     * Get classlabelsId by title
     */
    public function getClassIdByTitle( $title )
    {
        $labels = Classlabel::where('title','=',$title)->first();
        return $labels;
    }

    /**
     * Get classlabelsId by its title by dataset_id
     */
    public function getClassIdByTitleAndDatasetId( $title, $d_id )
    {
        $labels = Classlabel::where('title','=',$title)->where('dataset_id','like',$d_id)->first();
        return $labels;
    }

    /**
     * Get all labels of specific image by name/filename
     */
    public function getAllClassesOfImageNameOrFilename( $image_name, $image_filename )
    {
        $image = Image::where('name','=',$name)
                 ->orWhere('filename','=',$filename)->first();
        $labels = $this->getAllClassesOfImage( $image->id );
        return $labels;
    }

    /**
     * Get all images of user by id/email
     */
    public function getAllImageOfUser( $user_id, $user_email )
    {
        if(is_null($user_id)) {
            $user_id = User::where('email','=',$user_email)->first();
        }

        $images = Image::where('user_id','=',$user_id)->get();
        return $images;
    }

    /**
     * Get all images of current user
     */
    public function getAllImageOfCurrentUser()
    {
        $images = Image::where('user_id','=',Auth::id())->get();
        return $images;
    }

    /**
     * Update Class Labels
     */
    public function updateClassLabels($classlabels)
    {
        foreach ($classlabels as $label) {
            if (!(Classlabel::where('title', '=', $label)->exist())) {
                $classlabel = new Classlabel;
                $classlabel->title = $label;
                $classlabel->save();
            }
        }
    }

    /**
     * Add Class Labels of single Image
     */
    public function addClassLabelsOfImage($classlabels, $image_id)
    {
        foreach ($classlabels as $label) {
            $clabel = Classlabel::where('title', '=', $label)->first();

            $imageLabel = new ImageClasslabels;
            $imageLabel->class_id = $clabel->id;
            $imageLabel->image_id = $image_id;
            $imageLabel->save();
        }
    }

    /**
     * Add new Dataset
     */
    public function addDataset($name, $des)
    {
        $alreadyAdded = $this->getUserDatasetByName($name);
        if (is_null($alreadyAdded)) {
            $dataset = new UserDatasets;
            $dataset->user_id = Auth::id();
            $dataset->title = $name;
            $dataset->description = $des;
            $dataset->save();

            return $dataset;
        }

        return $alreadyAdded;
    }
}
