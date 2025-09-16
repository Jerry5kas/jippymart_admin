<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique(); // e.g. "site_name", "default_og_image"
            $table->text('setting_value')->nullable();
            $table->text('description')->nullable(); // Description of what this setting does
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('setting_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_settings');
    }
}
