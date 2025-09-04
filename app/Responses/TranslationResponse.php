<?php

namespace App\Responses;

use App\Core\Responses\AbstractResponse;

class TranslationResponse extends AbstractResponse
{

     public function getCreateResponseMessage() : String
    {
        return "Translation created successfully";
    }

    public function getListResponseMessage() : String
    {
        return "List of translations";
    }

    public function getUpdateResponseMessage() : String
    {
        return "Translation updated successfully";
    }

    public function getDeleteResponseMessage(): String
    {
        return "Translation deleted successfully";
    }

}
