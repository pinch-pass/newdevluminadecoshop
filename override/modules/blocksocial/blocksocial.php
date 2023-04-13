<?php

class BlockSocialOverride extends BlockSocial
{
	public function hookldfooter()
	{
		return		$this->hookDisplayFooter();
	}
}