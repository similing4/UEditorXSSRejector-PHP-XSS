<?php
  function remove_xss($val) {
		$xss = new \Org\Util\UEditorXSSRejector();
		return $xss->parse($val);
	}
