<?php

namespace BlackBox\Id;

/**
 * Generates random string IDs.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class RandomStringIdGenerator implements IdGenerator
{
    public function getId()
    {
        return uniqid();
    }
}
