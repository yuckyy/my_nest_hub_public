<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddonScreeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_screenings', function (Blueprint $table) {

            $table->string('applicantGuid', 255)->default('');
            $table->string('firstName', 255)->default('');
            $table->string('lastName', 255)->default('');
            $table->string('email', 255)->default('');
            $table->text('log_applicant')->nullable();
            $table->string('orderGuid', 255)->default('');
            $table->string('orderStatus', 255)->default('');
            $table->string('orderType', 255)->default('');
            $table->string('externalIdentifier', 255)->default('');
            $table->string('quickappApplicantLink', 255)->default('');
            $table->text('log_order')->nullable();
            $table->text('log_callback')->nullable();

            /*
                    {"orderGuid":"df29fa2e-7556-4b45-94f5-7c2ed2ee8eec",
                    "fileNumber":410828,
                    "orderStatus":"app-pending",
                    "orderType":"Employment",
                    "orderedDate":1616141596000,
                    "generalReportReference":"Mid-Group",
                    "externalIdentifier":"10001a",
                    "applicantName":"KOZYTSKYY, IVAN",
                    "clientName":"Sandbox API account for MYNESTHUB",
                    "clientCode":"424ac743-5ed8-448d-a2c6-6e86f7f55791",
                    "productName":"TazAPI - Tenant Product",
                    "requestedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent",
                    "searchFlagged":false,
                    "quickappApplicantLink":"https://lightning.instascreen.net/orderquickapp/index.taz?x=8sa9gr3eueibv2vj4mcbli7u9b24a022i6cbdQxKfcO5bLAf4RRMK7CDRPKIYjKPihnR4j9HoP1sFY8Mr&y=410828&z=1",
                    "createdDate":1616141596000,"createdBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent",
                    "modifiedDate":1616141596303,"modifiedBy":"Auto Setup Instance - Auto Setup Sandbox API account for ePayRent"}
                     */


            $table->bigIncrements('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addon_screenings');
    }
}
