<?php

namespace Inane\Stdlib;

use Inane\Stdlib\Enum\QRCodeType;

require_once __DIR__ . '/phpqrcode.php';

/**
 * Represents a QR Code with configurable text content.
 * This class allows the creation and manipulation of a QR Code by setting or getting its text content.
 * WIFI:
 *  WIFI:T:<AuthenticationType>;S:<SSID>;P:<Password>;;
 */
class QRObject {
	protected(set) QRCodeType $type {
		get => !isset($this->type) ? $this->type = QRCodeType::identifyType($this->text) : $this->type;
		set => $this->type = $value;
	}

	public function __construct(
		protected(set) ?string $text {
			get => $this->text ?? null;
			set => $this->text = $value;
		},
	) {
		$this->type = QRCodeType::identifyType($this->text);
	}

	/**
	 * QRCode as a base64 image
	 *
	 * @return string base64 string of QRCode
	 */
	public function getImageBase64(): string {
		$tmp = tempnam(sys_get_temp_dir(), 'qr-code-');
		\QRcode::png($this->text, $tmp, QR_ECLEVEL_H, 10, 1);
		$data = file_get_contents($tmp);
		unlink($tmp);

		$base64 = 'data:image/png;base64,' . base64_encode($data);

		return $base64;
	}
}