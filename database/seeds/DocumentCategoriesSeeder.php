<?php

use Illuminate\Database\Seeder;

class DocumentCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('document_categories')->truncate();

        DB::table('document_categories')->insert(['id' => '1', 'name' => 'Offers & Addendums','pid'=>'19']);
        DB::table('document_categories')->insert(['id' => '2', 'name' => 'Disclosures','pid'=>'19']);
        DB::table('document_categories')->insert(['id' => '3', 'name' => 'Appraisals','pid'=>'19']);
        DB::table('document_categories')->insert(['id' => '4', 'name' => 'Inspections','pid'=>'19']);
        DB::table('document_categories')->insert(['id' => '5', 'name' => 'Title, Surveys, & Plans','pid'=>'19']);
        DB::table('document_categories')->insert(['id' => '6', 'name' => 'Closing Statements','pid'=>'19']);
        DB::table('document_categories')->insert(['id' => '7', 'name' => 'Recorded Deeds','pid'=>'19']);
        DB::table('document_categories')->insert(['id' => '8', 'name' => 'Leases & Exhibits','pid'=>'20']);
        DB::table('document_categories')->insert(['id' => '9', 'name' => 'Apps & Credit Checks','pid'=>'20']);
        DB::table('document_categories')->insert(['id' => '10', 'name' => 'Noties & Correspondence','pid'=>'20']);
        DB::table('document_categories')->insert(['id' => '11', 'name' => 'Move In/Out Inspections','pid'=>'20']);
        DB::table('document_categories')->insert(['id' => '12', 'name' => 'Eviction & Legal Docs','pid'=>'20']);
        DB::table('document_categories')->insert(['id' => '13', 'name' => 'Mortgage & Loan Agmts','pid'=>'21']);
        DB::table('document_categories')->insert(['id' => '14', 'name' => 'Monthly Statements','pid'=>'21']);
        DB::table('document_categories')->insert(['id' => '15', 'name' => 'Form 1098s','pid'=>'21']);
        DB::table('document_categories')->insert(['id' => '16', 'name' => 'Quotes','pid'=>'22']);
        DB::table('document_categories')->insert(['id' => '17', 'name' => 'Policy Docs','pid'=>'22']);
        DB::table('document_categories')->insert(['id' => '18', 'name' => 'Certs & Binders','pid'=>'22']);

        DB::table('document_categories')->insert(['id' => '19', 'name' => 'Purchase & Sale']);
        DB::table('document_categories')->insert(['id' => '20', 'name' => 'Leases & Tenants']);
        DB::table('document_categories')->insert(['id' => '21', 'name' => 'Mortgages & Loans']);
        DB::table('document_categories')->insert(['id' => '22', 'name' => 'Insurance']);
    }
}

