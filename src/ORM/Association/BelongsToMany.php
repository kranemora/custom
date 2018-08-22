<?php
namespace Custom\ORM\Association;

class BelongsToMany extends \Cake\ORM\Association\BelongsToMany
{
	use PrefixSuffixBelongsToManyTrait;
}