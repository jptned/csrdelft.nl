<?php

/**
 * FotoAlbumModel.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 */
class FotoAlbumModel extends PersistenceModel {

	const orm = 'FotoAlbum';

	protected static $instance;

	/**
	 * Create database entry if fotoalbum does not exist.
	 * 
	 * @param PersistentEntity $album
	 * @param array $attributes
	 * @return mixed false on failure
	 */
	public function retrieveAttributes(PersistentEntity $album, array $attributes) {
		$this->verwerkFotos($album);
		return parent::retrieveAttributes($album, $attributes);
	}

	public function create(PersistentEntity $album) {
		mkdir($album->path);
		chmod($album->path, 0755);
		$album->owner = LoginModel::getUid();
		parent::create($album);
	}

	public function getFotoAlbum($path) {
		if (!endsWith($path, '/')) {
			$path .= '/';
		}
		$album = new FotoAlbum($path);
		if (!$album->magBekijken()) {
			return false;
		}
		if (!$album->exists()) {
			return null;
		}
		return $album;
	}

	public function verwerkFotos(FotoAlbum $album) {
		if (!$this->exists($album)) {
			$album->owner = LoginModel::getUid();
			$this->create($album);
		}
		$path = $album->path . '_thumbs';
		if (!file_exists($path)) {
			mkdir($path);
			chmod($path, 0755);
		}
		$path = $album->path . '_resized';
		if (!file_exists($path)) {
			mkdir($path);
			chmod($path, 0755);
		}
		foreach ($album->getFotos(true) as $foto) {
			FotoModel::instance()->verwerkFoto($foto);
		}
	}

	public function getMostRecentFotoAlbum() {
		$album = $this->getFotoAlbum(PICS_PATH . 'fotoalbum/');
		if (!$album) {
			return null;
		}
		return $album->getMostRecentSubAlbum();
	}

	public function hernoemAlbum(FotoAlbum $album, $nieuwenaam) {
		if (!valid_filename($nieuwenaam)) {
			throw new Exception('Ongeldige naam');
		}
		return rename($album->path, str_replace($album->dirname, $nieuwenaam, $album->path));
	}

	public function setAlbumCover(FotoAlbum $album, Foto $cover) {
		$ret = true;
		// find old cover
		foreach ($album->getFotos() as $foto) {
			if (strpos($foto->filename, 'folder') !== false) {
				if ($foto->getFullPath() === $cover->getFullPath()) {
					return $ret;
				}
				$old = $foto->getFullPath();
				$ret &= rename($old, str_replace('folder', '', $old));
				$old = $foto->getResizedPath();
				$ret &= rename($old, str_replace('folder', '', $old));
				$old = $foto->getThumbPath();
				$ret &= rename($old, str_replace('folder', '', $old));
			}
		}
		// set new cover
		$old = $cover->getFullPath();
		$ret &= rename($old, substr_replace($old, 'folder', strrpos($old, '.'), 0));
		$old = $cover->getResizedPath();
		$ret &= rename($old, substr_replace($old, 'folder', strrpos($old, '.'), 0));
		$old = $cover->getThumbPath();
		$ret &= rename($old, substr_replace($old, 'folder', strrpos($old, '.'), 0));
		return $ret;
	}

	public function cleanup() {
		foreach ($this->find() as $album) {
			if (!$album->exists()) {
				$this->delete($album);
			}
		}
	}

}

class FotoModel extends PersistenceModel {

	const orm = 'Foto';

	protected static $instance;

	/**
	 * Create database entry if foto does not exist.
	 * 
	 * @param PersistentEntity $foto
	 * @param array $attributes
	 * @return mixed false on failure
	 */
	public function retrieveAttributes(PersistentEntity $foto, array $attributes) {
		$this->verwerkFoto($foto);
		return parent::retrieveAttributes($foto, $attributes);
	}

	public function create(PersistentEntity $foto) {
		$foto->owner = LoginModel::getUid();
		$foto->rotation = 0;
		parent::create($foto);
	}

	public function verwerkFoto(Foto $foto) {
		if (!$this->exists($foto)) {
			$this->create($foto);
		}
		if (!$foto->hasThumb()) {
			$foto->createThumb();
		}
		if (!$foto->hasResized()) {
			$foto->createResized();
		}
	}

	public function verwijderFoto(Foto $foto) {
		$ret = true;
		$ret &= unlink($foto->directory->path . $foto->filename);
		if ($foto->hasResized()) {
			$ret &= unlink($foto->getResizedPath());
		}
		if ($foto->hasThumb()) {
			$ret &= unlink($foto->getThumbPath());
		}
		if ($ret) {
			$this->delete($foto);
		}
		return $ret;
	}

	public function cleanup() {
		foreach ($this->find() as $foto) {
			if (!$foto->exists()) {
				$this->delete($foto);
			}
		}
	}

}
