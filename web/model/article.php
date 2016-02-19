<?php

class Articles extends Model
{
	public $_table = 'articles';

	public $_properties = array(
		'id' => '',
		'title' => '',
		'content' => '',
		'created_at' => '',
		'updated_at' => '',
	);

	public function set_properties($request)
	{
		if ($this->id)
		{
			$this->_properties['id'] = $this->id;
			$this->_properties['title'] = $request->get('title');
			$this->_properties['content'] = $request->get('content');
			$this->_properties['created_at'] = $this->created_at;
			$this->_properties['updated_at'] = date('Y/m/d H:i:s');
		}
		else
		{
			$this->_properties['id'] = $this->id;
			$this->_properties['title'] = $request->get('title');
			$this->_properties['content'] = $request->get('content');
			$this->_properties['created_at'] = date('Y/m/d H:i:s');
			$this->_properties['updated_at'] = '';
		}
	}
}
