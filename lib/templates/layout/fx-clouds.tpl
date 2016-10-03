<script id="vs" type="x-shader/x-vertex">

	varying vec2 vUv;

	void main() {

	vUv = uv;
	gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );

	}
</script>

<script id="fs" type="x-shader/x-fragment">

	uniform sampler2D map;

	uniform vec3 fogColor;
	uniform float fogNear;
	uniform float fogFar;

	varying vec2 vUv;

	void main() {

	float depth = gl_FragCoord.z / gl_FragCoord.w;
	float fogFactor = smoothstep( fogNear, fogFar, depth );

	gl_FragColor = texture2D( map, vUv );
	gl_FragColor.w *= pow( gl_FragCoord.z, 20.0 );
	gl_FragColor = mix( gl_FragColor, vec4( fogColor, gl_FragColor.w ), fogFactor );

	}
</script>

<script type="text/javascript">

	if (!Detector.webgl) {
		Detector.addGetWebGLMessage();
	}

	var container;
	var camera, scene, renderer;
	var mesh, geometry, material;

	var mouseX = 0, mouseY = 0;
	var start_time = Date.now();

	var windowHalfX = window.innerWidth / 2;
	var windowHalfY = window.innerHeight / 2;

	initClouds();

	function initClouds() {

		container = $('#cd-main-overlay');

		// Bg gradient

		var canvas = document.createElement('canvas');
		canvas.width = 32;
		canvas.height = window.innerHeight;

		var context = canvas.getContext('2d');

		var gradient = context.createLinearGradient(0, 0, 0, canvas.height);
		gradient.addColorStop(0, "#1e4877");
		gradient.addColorStop(0.5, "#4584b4");

		context.fillStyle = gradient;
		context.fillRect(0, 0, canvas.width, canvas.height);

		container.css('background', 'url("' + canvas.toDataURL('image/png') + '")');

		//

		camera = new THREE.PerspectiveCamera(30, window.innerWidth / window.innerHeight, 1, 3000);
		camera.position.z = 3000;

		scene = new THREE.Scene();

		geometry = new THREE.Geometry();

		var texture = THREE.ImageUtils.loadTexture('/assets/layout/plaetjes/bg/cloud10.png', null, animateClouds);
		texture.magFilter = THREE.LinearMipMapLinearFilter;
		texture.minFilter = THREE.LinearMipMapLinearFilter;

		var fog = new THREE.Fog(0x4584b4, -100, 3000);

		material = new THREE.ShaderMaterial({
			uniforms: {
				"map": {
					type: "t",
					value: texture
				},
				"fogColor": {
					type: "c",
					value: fog.color
				},
				"fogNear": {
					type: "f",
					value: fog.near
				},
				"fogFar": {
					type: "f",
					value: fog.far
				}
			},
			vertexShader: document.getElementById('vs').textContent,
			fragmentShader: document.getElementById('fs').textContent,
			depthWrite: false,
			depthTest: false,
			transparent: true

		});

		var plane = new THREE.Mesh(new THREE.PlaneGeometry(64, 64));

		for (var i = 0; i < 8000; i++) {

			plane.position.x = Math.random() * 1000 - 500;
			plane.position.y = -Math.random() * Math.random() * 200 - 15;
			plane.position.z = i;
			plane.rotation.z = Math.random() * Math.PI;
			plane.scale.x = plane.scale.y = Math.random() * Math.random() * 1.5 + 0.5;

			THREE.GeometryUtils.merge(geometry, plane);

		}

		mesh = new THREE.Mesh(geometry, material);
		scene.add(mesh);

		mesh = new THREE.Mesh(geometry, material);
		mesh.position.z = -8000;
		scene.add(mesh);

		renderer = new THREE.WebGLRenderer({
			antialias: false
		});
		renderer.setSize(window.innerWidth, window.innerHeight);
		container.append(renderer.domElement);

		document.addEventListener('mousemove', onDocumentMouseMoveClouds, false);
		window.addEventListener('resize', onWindowResizeClouds, false);

	}

	function onDocumentMouseMoveClouds(event) {

		mouseX = (event.clientX - windowHalfX) * 0.25;
		mouseY = (event.clientY - windowHalfY) * 0.15;

	}

	function onWindowResizeClouds(event) {

		camera.aspect = window.innerWidth / window.innerHeight;
		camera.updateProjectionMatrix();

		renderer.setSize(window.innerWidth, window.innerHeight);

	}

	function animateClouds() {

		requestAnimationFrame(animateClouds);

		if (container.hasClass('is-visible')) {

			position = ((Date.now() - start_time) * 0.03) % 8000;

			camera.position.x += (mouseX - camera.position.x) * 0.005;
			camera.position.y += (-mouseY - 70 - camera.position.y) * 0.01;
			camera.position.z = -position + 8000;

			renderer.render(scene, camera);
		}
	}

</script>