<?php

/**
 * FotoAlbumView.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 * De views van het fotoalbum.
 */
class FotoAlbumView extends TemplateView {

	public function __construct(FotoAlbum $fotoalbum) {
		parent::__construct($fotoalbum);
	}

	function getTitel() {
		return 'Fotoalbum | ' . $this->model->mapnaam;
	}

	function view() {
		echo SimpleHTML::getMelding();
		$this->smarty->assign('album', $this->model);
		$this->smarty->display('MVC/fotoalbum/album.tpl');
	}

}

class FotoAlbumZijbalkView extends TemplateView {

	public function __construct(FotoAlbum $album) {
		parent::__construct($album);
	}

	public function view() {
		$url = $this->model->getUrl();
		echo '<div id="zijbalk_fotoalbum">';
		echo '<h1><a href="/actueel/fotoalbum/">Laatste fotoalbum</a></h1>';
		echo '<div class="item">';
		echo '<a href="' . $url . '">' . $this->model->mapnaam . '</a>';
		echo '<div class="fotos">';
		$fotos = $this->model->getFotos();
		$limit = sizeof($fotos);
		if ($limit > 6) {
			$limit = 6;
		}
		for ($i = 0; $i < $limit; $i++) {
			$foto = $fotos[$i];
			if ($foto instanceof Foto) {
				echo '<a href="/actueel/fotoalbum' . $url . '#' . $foto->bestandsnaam . '">';
				echo '<img src="' . $foto->getThumbURL() . '">';
				echo '</a>' . "\n";
			}
		}
		echo '</div>'; // class="fotos"
		echo '</div>'; // class="item"
		echo '</div>'; // id="zijbalk_fotoalbum"
	}

}

class FotoAlbumUbbView extends TemplateView {

	private $compact = false; //compact or expanded tag.
	private $rows = 2;  //number of rows
	private $per_row = 7;  //images per row
	private $big = array(); //array with index of the ones to enlarge
	private $picsize = 75;  //size of an image
	private $rowmargin = 2; //margin between the images

	public function __construct(FotoAlbum $album = null) {
		parent::__construct($album);
		if ($album === null OR ! $album->exists()) {
			$this->model = FotoAlbumModel::getMostRecentFotoAlbum();
		}
	}

	public function view() {
		echo $this->getHTML();
	}

	public function makeCompact() {
		$this->compact = true;
	}

	public function setRows($rows) {
		$this->rows = $rows;
	}

	/**
	 * One integer index or array of integer indexes of images to enlarge.
	 * possible 'macro' enlargements for up to 8 rows:
	 * - a (diagonals),
	 * - b (diagonals),
	 * - c (odd/even)
	 * 
	 * @param string $index
	 */
	public function setBig($index) {
		if (in_array($index, array('a', 'b', 'c'))) {
			switch ($index) {
				case 'a':
					$this->big = array(0, 9, 18, 28, 37, 46);
					break;
				case 'b':
					$this->big = array(0, 4, 15, 19, 28, 32, 43, 47);
					break;
				case 'c':
					$this->big = array(0, 16, 4, 28, 44, 32);
					break;
			}
			return;
		}
		if (count(explode(',', $index)) > 1) {
			//explode on ',' and convert tot int.
			$this->big = array_map('intval', explode(',', $index));
		} else {
			$this->big = array((int) $index);
		}
	}

	/**
	 * Build a grid with Foto-objects.
	 *
	 * The index is saved together with the object for correct reference
	 * in case the image is moved one left or one up in the grid at borders.
	 */
	private function getGrid() {
		$fotos = $this->model->getFotos();
		$grid = array_fill(0, $this->rows, array_fill(0, $this->per_row, null));
		// put big images on grid.
		if (count($this->big) > 0 && $this->rows > 1) {
			foreach ($this->big as $bigindex) {
				$row = floor($bigindex / $this->per_row);
				$col = ($bigindex % $this->per_row);
				// remove images that will cause wrap around.
				if ($col + 1 >= $this->per_row) {
					continue;
				}
				if ($row + 1 >= $this->rows) {
					continue;
				}
				// remove images that will cause overlap with a big image one row up.
				if ($grid[$row][$col + 1] == 'USED') {
					continue;
				}
				// if valid image, put on grid.
				if (isset($fotos[$bigindex]) && $fotos[$bigindex] instanceof Foto) {
					// if place already USED, do not put photo in.
					if ($grid[$row][$col] == 'USED') {
						continue;
					}
					$grid[$row][$col] = array(
						'index' => $bigindex,
						'foto' => $fotos[$bigindex]
					);
					// mark the three places overlapped by this image as used.
					$grid[$row + 1][$col] = $grid[$row][$col + 1] = $grid[$row + 1][$col + 1] = 'USED';
				}
			}
		}
		// put small images on grid.
		$row = $col = 0;
		foreach ($fotos as $key => $foto) {
			// do not put big pictures on grid again.
			if (in_array($key, $this->big)) {
				continue;
			}
			// find first free place.
			while ($grid[$row][$col] != null) {
				$col = $col + 1;
				// move to next row if end of row is reached.
				if ($col >= $this->per_row) {
					$row = $row + 1;
					$col = $col % $this->per_row;
					// break out of two loops if reached row limit.
					if ($row >= $this->rows) {
						break 2;
					}
				}
			}
			$grid[$row][$col] = array(
				'index' => $key,
				'foto' => $foto
			);
		}
		// check length of last row and remove it if not full and no big images overlap it.
		if (!in_array('USED', end($grid)) && count(array_filter(end($grid))) < $this->per_row) {
			unset($grid[$this->rows - 1]);
		}
		if (count(array_filter(end($grid))) == 0) {
			unset($grid[count($grid) - 1]);
		}
		return $grid;
	}

	public function getGridHtml() {
		$grid = $this->getGrid();
		$url = $this->model->getUrl();
		$delta = $this->picsize + (2 * $this->rowmargin);
		$ret = '<div class="images" style="height: ' . (count($grid) * $delta) . 'px">';
		foreach ($grid as $row => $rowcontents) {
			foreach ($rowcontents as $col => $foto) {
				if (is_array($foto)) {
					$ret .= '<a href="' . $url . '#' . direncode($foto['foto']->bestandsnaam) . '"';
					$ret.=in_array($foto['index'], $this->big) ? 'class="big"' : 'class="sml"';
					$ret .= 'style=" left: ' . ($delta * $col) . 'px; top: ' . ($delta * $row) . 'px;">';
					$ret .= '<img src="' . $foto['foto']->getThumbURL() . '">';
					$ret .= '</a>' . "\n";
				}
			}
		}
		$ret .= '</div>';
		return $ret;
	}

	public function getHTML() {
		$url = $this->model->getUrl();
		if ($this->compact) {
			// compacte versie van de tag is alleen een thumbnail.
			$content = '<a href="' . $url . '"><img src="' . $this->model->getThumbURL() . '" class="compact" /></a><div class="clear"></div>';
		} else {
			$content = $this->getGridHtml();
		}
		return '<div class="ubb_block ubb_fotoalbum"><h2>' . $this->model->getBreadcrumbs() . '&raquo; <a href="' . $url . '">' . $this->model->mapnaam . '</a></h2>' . $content . '</div>';
	}

}
