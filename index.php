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
			game.load.image('enemy_left', 'assets/enemies/blob_left.png');			
			game.load.image('enemy_right', 'assets/enemies/blob_right.png');			
			game.load.image('ground','assets/world/ground.png');
			game.load.image('healthKit','assets/items/firstaid.png');
			game.load.image('b_platform', 'assets/world/brick_platform.png');
			game.load.image('c_platform', 'assets/world/cobble_platform.png');
			game.load.image('jetpack', 'assets/items/jetpack.png');
			game.load.image('bullet', 'assets/items/bullet.png');

		}
		// objects
		var player;
		var platforms;
		var cursors;
		// text objects
		var scoreText;
		var healthText;
		var ammoText;
		var testText;

		// platform coordinates
		var locations = [[175,400],[525,350],[975,375]];
		var spawnLocations = [[1,530],[1260,530]];

		// random stuff
		var score = 0;
		var randNum;
		var spaceKey;
		var x;
		var y;
		var jetpack;
		var facing;

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
			player.health = 100;
			console.log(player);

			// adding gravity to player
			player.body.gravity.y = 400;
			player.body.collideWorldBounds = true;

			// Animations for player
			player.animations.add('left', [2,3,4], 10, true);
			player.animations.add('right', [5,6,7], 10, true);

			//Adding healthkits
			healthkits = game.add.group();
			healthkits.enableBody = true;

			bullets = game.add.group();
			bullets.enableBody = true;

			// Spawns powerups every ten seconds
			timer = game.time.events.repeat(Phaser.Timer.SECOND * 10, 50, spawnPowerUp);
			// changes the location of the powerup every five seconds
			timer = game.time.events.repeat(Phaser.Timer.SECOND * 0.5, 50, changeRand);
			// Spawns enemies
			// timer = game.time.events.repeat(Phaser.Timer.SECOND * 1, 50, spawnEnemy);

			// Activates keyboard
			cursors = game.input.keyboard.createCursorKeys();

			// Displays score in top left corner and health in right top corner
			scoreText = game.add.text(10, 10, 'Score: 0');
			ammoText = game.add.text(1110, 10, "Ammo: " + player.ammo);
			healthText = game.add.text(500, 10, "Health: " + player.health + "%")

			spaceKey = game.input.keyboard.addKey(Phaser.Keyboard.SPACEBAR);

		}

		function update() {
			healthText.text = "Health: " + player.health + "%";
			// var x = Math.round(player.x);
			// var y = Math.round(player.y);
			// testText.text = x + "," + y;

		    //  Collisions
		    game.physics.arcade.collide(player, platforms);
		    game.physics.arcade.collide(platforms, healthkits);
		    game.physics.arcade.collide(enemies, platforms);
		    game.physics.arcade.collide(player, enemies, enemyHurtsPlayer);

		    // Overlaps for collecting objects
		    game.physics.arcade.overlap(player, healthkits, collectHealth, null, this);
		    game.physics.arcade.overlap(player, enemies, enemyHurtsPlayer, null, this);

		    //  Reset the players velocity (movement)
		    player.body.velocity.x = 0;

		    // Controls
		    if (cursors.left.isDown)
		    {
		        player.body.velocity.x = -150;

		        player.animations.play('left');
		        facing = "left";
		    }
		    else if (cursors.right.isDown)
		    {
		        player.body.velocity.x = 150;

		        player.animations.play('right');
		        facing = "right";
		    }
		    else
		    {
		        //  Stand still
		        player.animations.stop();

		        player.frame = 0;
		    }
		    
		    if (spaceKey.isDown) {
		    	shoot();
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
			}
		}

		function spawnPowerUp() {
			if (randNum < 0.40)
			{
				var kit = healthkits.create(locations[0][0], locations[0][1], 'healthKit');
			}
			else if (randNum > 0.39 && randNum < 0.85)
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

		function spawnEnemy() {
			if (randNum <= 0.5)
			{
				var enemy = enemies.create(spawnLocations[0][0], spawnLocations[0][1], 'enemy_left');
				enemy.body.velocity.x = 300;
			}else if (randNum > 0.5)
			{
				var enemy = enemies.create(spawnLocations[1][0], spawnLocations[1][1], 'enemy_right');
				enemy.body.velocity.x = -300;
			}
			enemy.body.gravity.y = 300;
		}

		function enemyHurtsPlayer(player, enemy){
			enemy.kill();
			player.health -= 25;
			console.log(player);
			if (player.health <= 0){
				player.kill();
			};
		}

		var shotTimer = 0;
		function shoot() {
			if (shotTimer < game.time.now) {
				shotTimer = game.time.now +275;
				var bullet;
				if (facing = "right"){
					bullet = bullets.create(player.body.x + 50, player.body.y + 25, 'bullet');
				}else {
					bullet = bullets.create(player.body.x, player.body.y + 25, 'bullet');
				}
				bullet.outOfBoundsKill = true;
				bullet.anchor.setTo(0.5, 0.5);
				bullet.body.velocity.y = 0;
				if (facing === "right"){
					bullet.body.velocity.x = 400;
				} else if(facing === "left") {
					bullet.body.velocity.x = -400;
				}
			}
		}
		</script>
	</body>
</html>