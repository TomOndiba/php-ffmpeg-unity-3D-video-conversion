<?php
class Resolution {
    private $_resolutions = array(
        array('320', '200'),
        array('320', '240'),
        array('480', '640'),
        array('480', '800'),
        array('640', '480'),
        array('640', '960'),
        array('800', '1280'),
        array('2048', '1536')
    );

    private $_width;
    private $_height;

    public function __construct($width, $height) {
        $this->setSize($width, $height);
    }

    public function getMinimumMatch($revertToLargest = false, $matchAspectRatio = true) {
        if ($matchAspectRatio) {
            $aspect = $this->_width/$this->_height;
            foreach ($this->_resolutions as $res) {
                if ($res[0]/$res[1] == $aspect) {
                    if ($this->_width > $res[0] || $this->_height >     $res[1]) {
                        return ($revertToLargest ? $res : false);
                    }
                    return $res;
                }
            }
        }
        foreach ($this->_resolutions as $i => $res) {
            if ($this->_width <= $res[0]) {
                $total = count($this->_resolutions);
                for ($j = $i; $j < $total; $j++) {
                    if ($this->_height <= $this->_resolutions[$j][1]) {
                        return $this->_resolutions[$j];
                    }
                }
            }
        }
        return ($revertToLargest ? end($this->_resolutions) : false);
    }

    /**
     * Get the resolution
     *
     * @return array The resolution width/height as an array
     */
    public function getSize() {
        return array($this->_width, $this->_height);
    }

    /**
     * Set the resolution
     *
     * @param  int $width
     * @param  int $height
     * @return array The new resolution width/height as an array
     */
    public function setSize($width, $height) {
        $this->_width = abs(intval($width));
        $this->_height = abs(intval($height));
        return $this->getSize();
    }

    /**
     * Get the standard resolutions
     *
     * @return array
     */
    public function getStandardResolutions() {
        return $this->_resolutions;
    }

    /**
     * Set the standard resolution values
     *
     * @param  array An array of resolution width/heights as sub-arrays
     * @return array
     */
    public function setStandardResolutions(array $resolutions) {
        $this->_resolutions = $resolutions;
        return $this->_resolutions;
    }
}
?>
