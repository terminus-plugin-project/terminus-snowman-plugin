<?php

namespace TerminusPluginProject\Genie\Commands;

use Pantheon\Terminus\Commands\Site\SiteCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

class SnowmanCommand extends SiteCommand implements SiteAwareInterface
{
  use SiteAwareTrait;

  /**
   * Unfreezes a Pantheon website.
   *
   * @authorize
   *
   * @command snowman
   * @aliases site:unfreeze
   *
   * @param string $site Site to unfreeze
   *
   * @usage terminus snowman <site>
   */
  public function snowman($site)
  {
    /** @var \Pantheon\Terminus\Models\Site $site */
    $site = $this->getSite($site);

    return $site->getWorkflows()->create('unfreeze_site');
  }

}