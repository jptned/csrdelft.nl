<?php
namespace CsrDelft\view\formulier\uploadvelden;
use CsrDelft\model\entity\Afbeelding;
use CsrDelft\model\entity\Bestand;
use function CsrDelft\url_like;
use CsrDelft\view\formulier\invoervelden\UrlField;
use CsrDelft\view\formulier\UrlDownloader;
use Exception;


/**
 * DownloadUrlField.class.php
 *
 * @author C.S.R. Delft <pubcie@csrdelft.nl>
 * @author P.W.G. Brussee <brussee@live.nl>
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 30/03/2017
 *
 * Een bestand downloaden van een url, met file_get_contents of de
 * cURL-extensie. Als beide niet beschikbaar zijn wordt het formulier-
 * element niet weergegeven.
 *
 */
class DownloadUrlField extends UrlField {

	public $filterMime;
	private $downloader;
	private $tmp_file;

	public function __construct($name, array $filterMime) {
		parent::__construct($name, 'http://', 'Downloaden van URL');
		$this->filterMime = $filterMime;
		$this->downloader = new UrlDownloader();
		if ($this->isPosted()) {
			if (!url_like($this->value)) {
				return;
			}
			$data = $this->downloader->file_get_contents($this->value);
			if (empty($data)) {
				return;
			}
			$url_name = substr(trim($this->value), strrpos($this->value, '/') + 1);
			$clean_name = preg_replace('/[^a-zA-Z0-9\s\.\-\_]/', '', $url_name);
			$this->tmp_file = TMP_PATH . $clean_name;
			if (!is_writable(TMP_PATH)) {
				throw new Exception('TMP_PATH is niet beschrijfbaar');
			}
			$filesize = file_put_contents($this->tmp_file, $data);
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($finfo, $this->tmp_file);
			finfo_close($finfo);
			if (in_array($mimetype, Afbeelding::$mimeTypes)) {
				$this->model = new Afbeelding($this->tmp_file);
			} else {
				$this->model = new Bestand();
				$this->model->filename = $clean_name;
				$this->model->filesize = $filesize;
				$this->model->mimetype = $mimetype;
				$this->model->directory = TMP_PATH;
			}
		}
	}

	public function isAvailable() {
		return $this->downloader->isAvailable();
	}

	public function getFilter() {
		return $this->filterMime;
	}

	public function validate() {
		parent::validate();
		if (!$this->isAvailable()) {
			$this->error = 'PHP.ini configuratie: fsocked, cURL of allow_url_fopen moet aan staan.';
		} elseif (!url_like($this->value)) {
			$this->error = 'Ongeldige url';
		} elseif (!$this->model instanceof Bestand OR !$this->model->exists() OR empty($this->model->filesize)) {
			$error = error_get_last();
			$this->error = $error['message'];
		} elseif (!empty($this->filterMime) AND !in_array($this->model->mimetype, $this->filterMime)) {
			$this->error = 'Bestandstype niet toegestaan: ' . $this->model->mimetype;
		}
		return $this->error === '';
	}

	public function opslaan($directory, $filename, $overwrite = false) {
		parent::opslaan($directory, $filename, $overwrite);
		$copied = copy($this->model->directory . $this->model->filename, $directory . $filename);
		if (!$copied) {
			throw new Exception('Bestand kopieren mislukt: ' . htmlspecialchars($this->model->directory . $this->model->filename));
		}
		$moved = unlink($this->model->directory . $this->model->filename);
		if (!$moved) {
			throw new Exception('Verplaatsen mislukt: ' . htmlspecialchars($this->model->directory . $this->model->filename));
		}
		if (false === @chmod($directory . $filename, 0644)) {
			throw new Exception('Geen eigenaar van bestand: ' . htmlspecialchars($directory . $filename));
		}
		$this->model->directory = $directory;
		$this->model->filename = $filename;
	}

	public function getHtml() {
		return '<input ' . $this->getInputAttribute(array('type', 'id', 'name', 'class', 'value', 'origvalue', 'disabled', 'readonly')) . '/>';
	}

}