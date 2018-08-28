<?php

function oxNew($className) {
    return oxRegistry::get($className);
}
