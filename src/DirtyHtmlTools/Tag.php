<?php 
namespace DirtyHtmlTools;

class Tag extends Element
{
    public $name;
    public $children = array();
    public $attributes = array();
	public $isRoot = false;
	public $filtered = false;

    public function __construct()
    {
        $this->type = ElementType::Tag;
    }

    public function ToHtml()
    {
        $sb = '';
        if ($this->filtered || $this->isRoot) {
            foreach ($this->children as $item)
            {
                $sb .= $item->ToHtml();
            }
            return $sb;
        }
        
        
        $sb .= '<';
        $sb .= $this->name;

        foreach ($this->attributes as $key => $val)
        {
            $sb .= ' ';
            $sb .= $key;
            if ($val !== null)
            {
                $sb .= '=';
                $sb .= '"';
                $sb .= htmlspecialchars($val);
                $sb .= '"';
            }
        }

        if (count($this->children) === 0)
        {
            $sb .= "/>";
        }
        else
        {
            $sb .= '>';

            foreach ($this->children as $item)
            {
                $sb .= $item->ToHtml();
            }

            $sb .= "</";
            $sb .= $this->name;
            $sb .= '>';
        }

        return $sb;
    }

    public function __toString()
    {
        return "T: {$this->name}";
    }
}