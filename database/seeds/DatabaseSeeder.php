<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CategoriesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(CronjobTypesTableSeeder::class);
        $this->call(CronjobsTableSeeder::class);
        $this->call(EmailTemplatesTableSeeder::class);
        $this->call(HelpPagesTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(PaymentGatewaysTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(SecurityQuestionsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(SkillsTableSeeder::class);
        $this->call(StaticPagesTableSeeder::class);
        $this->call(TimezonesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
