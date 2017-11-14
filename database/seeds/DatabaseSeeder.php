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
    	 // Model::unguard();
        // $this->call(UsersTableSeeder::class);

        $this->UserRoleSeeder();
        $this->EventTableSeeder();
    }

    function EventTableSeeder()
    {
    	DB::table('event')->insert(array('name'=>'Annual Buffalo Roundup', 			'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Ati-atihan', 						'country'=>'Philippines'));
    	DB::table('event')->insert(array('name'=>'Ballon Fiesta', 					'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Basel Fasnacht', 					'country'=>'Switzerland'));
    	DB::table('event')->insert(array('name'=>'Boston Marathon', 				'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Bud Billiken', 					'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Buenos Aires Tango Festival', 	'country'=>'Argentina'));
    	DB::table('event')->insert(array('name'=>'Carnaval de Dunkerque', 			'country'=>'France'));
    	DB::table('event')->insert(array('name'=>'Carnival of Venice', 				'country'=>'Italy'));
    	DB::table('event')->insert(array('name'=>'Carnivale Rio', 					'country'=>'Brazil'));

    	DB::table('event')->insert(array('name'=>'Castellers', 						'country'=>'Spain'));
    	DB::table('event')->insert(array('name'=>'Chinese New Year', 				'country'=>'China'));
    	DB::table('event')->insert(array('name'=>'Correfocs', 						'country'=>'Spain'));
    	DB::table('event')->insert(array('name'=>'Desert Festival of Jaisalmer', 	'country'=>'India'));
    	DB::table('event')->insert(array('name'=>'Desfile de Silleteros', 			'country'=>'Colombia'));
    	DB::table('event')->insert(array('name'=>'Da de los Muertos', 				'country'=>'Maxico'));
    	DB::table('event')->insert(array('name'=>'Diada de Sant Jordi', 			'country'=>'Spain'));
    	DB::table('event')->insert(array('name'=>'Diwali Festival of Lights', 		'country'=>'India'));
    	DB::table('event')->insert(array('name'=>'Falles', 							'country'=>'Spain'));
    	DB::table('event')->insert(array('name'=>'Festa del Renaixement', 			'country'=>'Spain'));

    	DB::table('event')->insert(array('name'=>'Festival de la Marinera', 		'country'=>'Peru'));
    	DB::table('event')->insert(array('name'=>'Inti Raymi', 						'country'=>'Peru'));
    	DB::table('event')->insert(array('name'=>'Fiesta de la Candelaria', 		'country'=>'Peru'));
    	DB::table('event')->insert(array('name'=>'Gion matsuri', 					'country'=>'Japan'));
    	DB::table('event')->insert(array('name'=>'Harbin Ice and Snow Festival', 	'country'=>'China'));
    	DB::table('event')->insert(array('name'=>'Heiva', 							'country'=>'Tahiti'));
    	DB::table('event')->insert(array('name'=>'Helsinki Samba Carnaval', 		'country'=>'Finland'));
    	DB::table('event')->insert(array('name'=>'Holi Festival', 					'country'=>'India'));
    	DB::table('event')->insert(array('name'=>'Infiorata di Genzano', 			'country'=>'Italy'));
    	DB::table('event')->insert(array('name'=>'La Tomatina', 					'country'=>'Spain'));

    	DB::table('event')->insert(array('name'=>'Lewes Bonfire', 					'country'=>'England'));
    	DB::table('event')->insert(array('name'=>'Macys Thanksgiving', 				'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Maslenitsa', 						'country'=>'Russia'));
    	DB::table('event')->insert(array('name'=>'Midsommar', 						'country'=>'Sweden'));
    	DB::table('event')->insert(array('name'=>'Notting hill carnival', 			'country'=>'England'));
    	DB::table('event')->insert(array('name'=>'Obon Festival', 					'country'=>'Japan'));
    	DB::table('event')->insert(array('name'=>'Oktoberfest', 					'country'=>'Germany'));
    	DB::table('event')->insert(array('name'=>'Onbashira Festival', 				'country'=>'Japan'));
    	DB::table('event')->insert(array('name'=>'Pingxi Lantern Festival', 		'country'=>'Taiwan'));
    	DB::table('event')->insert(array('name'=>'Pushkar Camel Festival', 			'country'=>'India'));

    	DB::table('event')->insert(array('name'=>'Quebec Winter Carnival', 			'country'=>'Canada'));
    	DB::table('event')->insert(array('name'=>'Queens Day', 						'country'=>'Netherlands'));
    	DB::table('event')->insert(array('name'=>'Rath Yatra', 						'country'=>'India'));
    	DB::table('event')->insert(array('name'=>'SandFest', 						'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'San Fermin', 						'country'=>'Spain'));
    	DB::table('event')->insert(array('name'=>'Songkran Water Festival', 		'country'=>'Thailand'));
    	DB::table('event')->insert(array('name'=>'St Patrick’s Day', 				'country'=>'Ireland'));
    	DB::table('event')->insert(array('name'=>'The battle of the Oranges', 		'country'=>'Italy'));
    	DB::table('event')->insert(array('name'=>'Timkat', 							'country'=>'Ethiopia'));
    	DB::table('event')->insert(array('name'=>'Viking Festival', 				'country'=>'Norway'));

    	DB::table('event')->insert(array('name'=>'July 4th', 						'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'AfrikaBurn', 						'country'=>'South Africa'));
    	DB::table('event')->insert(array('name'=>'Aomori nebuta', 					'country'=>'Japan'));
    	DB::table('event')->insert(array('name'=>'Apokries', 						'country'=>'Greece'));
    	DB::table('event')->insert(array('name'=>'Asakusa Samba Carnival', 			'country'=>'Japan'));
    	DB::table('event')->insert(array('name'=>'Australia day', 					'country'=>'Australia'));
    	DB::table('event')->insert(array('name'=>'Bastille day', 					'country'=>'France'));
    	DB::table('event')->insert(array('name'=>'Beltane Fire', 					'country'=>'Scotland'));
    	DB::table('event')->insert(array('name'=>'Boryeong Mud', 					'country'=>'South Korea'));
    	DB::table('event')->insert(array('name'=>'Carnaval de Oruro', 				'country'=>'Bolivia'));

    	DB::table('event')->insert(array('name'=>'Carnevale Di Viareggio', 			'country'=>'Italy'));
    	DB::table('event')->insert(array('name'=>'Cascamorras', 					'country'=>'Spain'));
    	DB::table('event')->insert(array('name'=>'Cheongdo Bullfighting Festival', 	'country'=>'South Korea'));
    	DB::table('event')->insert(array('name'=>'Crop over', 						'country'=>'Barbados'));
    	DB::table('event')->insert(array('name'=>'Eid al-Adha', 					'country'=>'Egypt'));
    	DB::table('event')->insert(array('name'=>'Eid al-Fitr', 					'country'=>'Iraq'));
    	DB::table('event')->insert(array('name'=>'Epiphany', 						'country'=>'Greece'));
    	DB::table('event')->insert(array('name'=>'Festa Della Sensa', 				'country'=>'Italy'));
    	DB::table('event')->insert(array('name'=>'Frozen Dead Guy Days', 			'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Galugan', 						'country'=>'Indonesia'));

    	DB::table('event')->insert(array('name'=>'Grindelwald Snow Festival', 		'country'=>'Switzerland'));
    	DB::table('event')->insert(array('name'=>'Hajj', 							'country'=>'Saudi Arabia'));
    	DB::table('event')->insert(array('name'=>'Halloween Festival of the Dead', 	'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Highland Games', 					'country'=>'Scotland'));
    	DB::table('event')->insert(array('name'=>'Junkanoo', 						'country'=>'Bahamas'));
    	DB::table('event')->insert(array('name'=>'Kaapse Klopse', 					'country'=>'South Africa'));
    	DB::table('event')->insert(array('name'=>'Keene Pumpkin Festival', 			'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Krampusnacht Festival', 			'country'=>'Austria'));
    	DB::table('event')->insert(array('name'=>'Los Diablos danzantes', 			'country'=>'Venezuela'));
    	DB::table('event')->insert(array('name'=>'Magh Mela', 						'country'=>'India'));

    	DB::table('event')->insert(array('name'=>'Mardi Gras', 						'country'=>'USA'));
    	DB::table('event')->insert(array('name'=>'Monkey Buffet Festival', 			'country'=>'Thailand'));
    	DB::table('event')->insert(array('name'=>'Naadam Festival', 				'country'=>'Mongolia'));
    	DB::table('event')->insert(array('name'=>'Passover', 						'country'=>'Israel'));
    	DB::table('event')->insert(array('name'=>'Pflasterspektakel', 				'country'=>'Austria'));
    	DB::table('event')->insert(array('name'=>'Phi Ta Khon', 					'country'=>'Thailand'));
    	DB::table('event')->insert(array('name'=>'Sahara Festival', 				'country'=>'Tunisia'));
    	DB::table('event')->insert(array('name'=>'Sapporo Snow Festival', 			'country'=>'Japan'));
    	DB::table('event')->insert(array('name'=>'Spice Mas Carnival', 				'country'=>'Grenada'));
    	DB::table('event')->insert(array('name'=>'Sweden Medieval Week', 			'country'=>'Sweden'));

    	DB::table('event')->insert(array('name'=>'Tamborrada', 						'country'=>'Spain'));
    	DB::table('event')->insert(array('name'=>'Tapati rapa Nui', 				'country'=>'Chile'));
    	DB::table('event')->insert(array('name'=>'Thaipusam', 						'country'=>'India'));
    	DB::table('event')->insert(array('name'=>'Thrissur Pooram', 				'country'=>'India'));
    	DB::table('event')->insert(array('name'=>'Tokushima Awa Odori Festival', 	'country'=>'Japan'));
    	DB::table('event')->insert(array('name'=>'Tour de France', 					'country'=>'France'));
    	DB::table('event')->insert(array('name'=>'Up Helly Aa Fire Festival', 		'country'=>'Scotland'));
    	DB::table('event')->insert(array('name'=>'Vancouver Symphony of Fire', 		'country'=>'Canada'));
    	DB::table('event')->insert(array('name'=>'Waisak day', 						'country'=>'Indonesia'));
    	DB::table('event')->insert(array('name'=>'Non-class'));

    }

    function UserRoleSeeder()
    {
        DB::table('user_role')->insert(array('role_name'=>'Admin'));
        DB::table('user_role')->insert(array('role_name'=>'User'));
    }
}
