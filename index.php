<!doctype HTML>
<html>
	<head>
		<meta charset = "UTF-8"/>
		<title>Game</title>
		<script type="text/javascript" src="js/phaser.js"></script>
	</head>
	<body>
		<script type="text/javascript">
		var game = new Phaser.Game(1280, 640, Phaser.AUTO, '', { preload: preload, create: create, update: update });
		function preload() {

			game.load.spritesheet('player', 'assets/player/player2.png',51,46);
			game.load.image('enemy', 'assets/enemies/blob.png');			
			game.load.image('ground','assets/world/ground.png');
			game.load.image('healthKit','assets/items/firstaid.png');
			game.load.image('b_platform', 'assets/world/brick_platform.png');
			game.load.image('c_platform', 'assets/world/cobble_platform.png');

		}
		// objects
		var player;
		var enemies;
		var platforms;
		var cursors;
		// text objects
		var scoreText;
		var healthText;
		var ammoText;
		var testText;

		// platform coordinates
		var locations = [[175,400],[525,350],[975,375]];

		// random stuff
		var health = 100;
		var score = 0;
		var randNum;
		var key1;
		var x;
		var y;

		function create() {
			// timeStart = game.time.now;
			// var lastUpdate = 0;

			randNum = Math.random();
			//Enabling physics
			game.physics.startSystem(Phaser.Physics.ARCADE);

			//Adding backgorund colors
			game.stage.backgroundColor = '#6888FE';

			enemies = game.add.group();
			platforms = game.add.group();

			// Enable physics for this group
			enemies.enableBody = true;
			platforms.enableBody = true;

			// Creating the ground
			var ground = platforms.create(0, game.world.height - 64, 'ground');
			ground.body.immovable = true;

			// Creating ledges
			var ledge = platforms.create(100, 450,'b_platform');
			ledge.body.immovable = true;

			var ledge = platforms.create(450, 400,'c_platform');
			ledge.body.immovable = true;

			var ledge = platforms.create(850, 200, 'b_platform');
			ledge.body.immovable = true;

			var ledge = platforms.create(900, 425, 'c_platform');
			ledge.body.immovable = true;

			// Create player
			player = game.add.sprite(640, game.world.height - 150, 'player');
			game.physics.arcade.enable(player);
			player.health = 60;
			player.ammo = 100;

			var enemy = enemies.create(100, game.world.height - 150, 'enemy');
			game.physics.arcade.enable(enemy);
			enemy.body.gravity.y = 400;
			enemy.body.collideWorldBounds = true;

			// adding gravity to player
			player.body.gravity.y = 400;
			player.body.collideWorldBounds = true;

			// Animations for player
			player.animations.add('left', [2,3,4], 10, true);
			player.animations.add('right', [5,6,7], 10, true);

			//Adding healthkits
			healthkits = game.add.group();
			healthkits.enableBody = true;

			// Spawns powerups every ten seconds
			timer = game.time.events.repeat(Phaser.Timer.SECOND * 10, 50, spawnPowerUp);
			// changes the location of the powerup every five seconds
			timer = game.time.events.repeat(Phaser.Timer.SECOND * 5, 50, changeRand);

			// Activates keyboard
			cursors = game.input.keyboard.createCursorKeys();

			// Displays score in top left corner and health in right top corner
			scoreText = game.add.text(10, 10, 'Score: 0');
			healthText = game.add.text(1110, 10, 'Health: ' + player.health + "%");
			ammoText = game.add.text(1110, 40, "Ammo: " + player.ammo);
			testText = game.add.text(640, 10, "x,y")

			key1 = game.input.keyboard.addKey(Phaser.Keyboard.SPACEBAR);

		}

		function update() {
			var x = Math.round(player.x);
			var y = Math.round(player.y);
			testText.text = x + "," + y;

		    //  Collide the player with objects in game
		    game.physics.arcade.collide(player, platforms);
		    game.physics.arcade.collide(platforms, healthkits);
		    game.physics.arcade.collide(enemies, platforms);

		    game.physics.arcade.overlap(player, healthkits, collectHealth, null, this);
		    game.physics.arcade.overlap(player, enemies, enemyKillsPlayer, null, this);

		    //  Reset the players velocity (movement)
		    player.body.velocity.x = 0;

		    if (cursors.left.isDown)
		    {
		        //  Move to the left
		        player.body.velocity.x = -150;

		        player.animations.play('left');
		    }
		    else if (cursors.right.isDown)
		    {
		        //  Move to the right
		        player.body.velocity.x = 150;

		        player.animations.play('right');
		    }
		    else
		    {
		        //  Stand still
		        player.animations.stop();

		        player.frame = 0;
		    }
		    
		     // Allow the player to jump if they are touching the ground.
		    if (cursors.up.isDown && player.body.touching.down)
		    {
		        player.body.velocity.y = -350;
		    }
		}

		function collectHealth(player, healthKit) {
			//Removes the kit from the screen
			healthKit.kill();

			// If user has less then 100 health then give him +10
			if (player.health < 100)
			{
				player.health += 10;
				// Makes sure health does not go over 100.
				if (player.health > 100)
				{
					player.health = 100;
				};
				healthText.text = "Health: " + player.health + "%";
			};
		}

		function spawnPowerUp() {
			if (randNum < 0.33)
			{
				var kit = healthkits.create(locations[0][0], locations[0][1], 'healthKit');
			}
			else if (randNum > 0.33 && randNum < 0.66)
			{
				var kit = healthkits.create(locations[1][0], locations[1][1], 'healthKit');
			}
			else
			{
				var kit = healthkits.create(locations[2][0], locations[2][1], 'healthKit');
			}
			kit.body.gravity.y = 300;
		}

		function changeRand() {
			randNum = Math.random();
		}

		function enemyAI() {

		}

		function enemyKillsPlayer(){
			player.kill();
		}
		</script>
	</body>
</html>