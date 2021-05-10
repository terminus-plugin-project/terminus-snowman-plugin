<?php

/**
 * Creates a Terminus command to defrost sites in Pantheon.
 */

namespace TerminusPluginProject\Snowman\Commands;

use Exception;
use Pantheon\Terminus\Commands\Site\SiteCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Models\Site;

class SnowmanCommand extends SiteCommand implements SiteAwareInterface
{
  use SiteAwareTrait;

  /**
   * The site identifier used for commands.
   *
   * @var string
   */
  private $siteName;

  /**
   * The current site instance.
   * 
   * @var Site
   */
  private $siteInstance;

  /**
   * Unfreezes a Pantheon website for a given name, URL, or Site ID.
   *
   * @authorize
   *
   * @command site:unfreeze
   * @aliases site:thaw, site:defrost, snowman
   * @usage terminus site:unfreeze <site>
   * 
   * @param string $site Site (name, URL, or ID) to unfreeze.
   */
  public function snowman(string $site): self
  {
    try {
      $this->setSiteInstance($site);
    } catch (\Throwable $th) {
      $this->io()->error($th->getMessage());
    }

    return $this->runner();
  }

  /**
   * Sets the normalized site name for this instance.
   *
   * @param string $siteName The name, URL, or ID of the site.
   * @return $this
   */
  protected function setSiteName(string $siteName): self
  {
    $this->siteName = $this->normalizeSiteName($siteName);

    return $this;
  }

  /**
   * Gets the site name for this run.
   * 
   * @return string The normalized site name.
   * @throws Exception If the site name isn't set.
   */
  protected function getSiteName(): string
  {
    if (!empty($this->siteName)) {
      return (string) $this->siteName;
    }

    return '';
  }

  /**
   * Sets up an instance of the Pantheon site.
   *
   * @param string $siteName The site name, URL, or ID.
   * @return $this
   */
  protected function setSiteInstance(string $siteName): self
  {
    try {
      $this->setSiteName($siteName);

      if (!$this->siteInstance instanceof Site) {
        $this->siteInstance = $this->getSite($this->getSiteName());
      }

      // Re-set the site name with the one in Pantheon's data.
      $this->setSiteName($this->siteInstance->get('name'));
    } catch (\Throwable $th) {
      $this->io()->error($th->getMessage());
    }


    return $this;
  }

  /**
   * Gets the current instance of the site for this command run.
   * 
   * @return Site The Pantheon site instance.
   * @throws Exception If the site can't be found.
   */
  protected function getSiteInstance(): Site
  {
    if (!$this->siteInstance instanceof Site) {
      throw new \Exception(sprintf('Could not find the site %s', $this->siteName), 'invalid_site');
    }

    return $this->siteInstance;
  }

  /**
   * Gets the site name from a string.
   * 
   * @param string $maybeSiteName The URL, UUID, or name-like string.
   * @return string The name for use in site commands.
   * @throws Exception If a slug can't be generated.
   */
  public function normalizeSiteName(string $maybeSiteName): string
  {
    $siteName = trim($maybeSiteName);

    // No need to continue if we already have a site slug that matches what's trying to be set.
    if (!empty($this->siteName) && $this->getSiteName() === $maybeSiteName) {
      return $maybeSiteName;
    }

    // Assume UUID format is a Pantheon Site ID and skip further processing.
    if ($this->isUUID($siteName)) {
      return $maybeSiteName;
    }

    try {
      $regexes = [
        // URLs or name with leading env, up to the site name.
        '/^(https?:\/\/)?(dev|test|live)-/i',
        // Names with trailing ".env" after site name.
        '/\.(dev|test|live)/i',
        // Platform URLs and anything after.
        '/\.pantheonsite\.io(?:.+)?/i'
      ];

      $siteName = preg_replace($regexes, '', $siteName);

      if (empty($siteName)) {
        throw new \Exception(sprintf('%s is an invalid name.', $siteName), 'invalid_site');
      }
    } catch (\Throwable $th) {
      $this->io()->error($th->getMessage());
    }

    return $siteName;
  }

  /**
   * Checks to see if this is a valid UUID format. It does not check for Site ID validity.
   * 
   * @param string $string The string to check.
   * @return bool Whether this matches the UUID format.
   */
  public function isUUID(string $string): bool
  {
    $maybeUUID = trim($string);
    // This regex can probably be shortened, but this one only takes 16 steps.
    $uuid_regex = '/\b[\da-f]{8}-(?:[\da-f]{4}-){3}[\da-f]{12}\b/i';
    return (1 === preg_match($uuid_regex, $maybeUUID));
  }

  /**
   * Runs the command to unfreeze a site.
   * 
   * @return $this
   * @throws Exception If the workflow fails.
   */
  private function thaw(): self
  {
    try {
      $this->getSiteInstance()->getWorkflows()->create('unfreeze_site');
    } catch (\Throwable $th) {
      $this->io()->error($th->getMessage());
    }

    return $this;
  }

  /**
   * Shows a friendly message about the frozen status of the site.
   * 
   * @return $this 
   */
  private function showFrozenMessage(): self
  {
    $chilly = $this->getSiteInstance()->isFrozen() ? 'yes' : 'no';
    $this->io()->note(sprintf('Is %1$s frozen? %2$s', $this->siteName, $chilly));

    return $this;
  }

  /**
   * Runs the commands to unfreeze a site.
   *
   * @return $this
   */
  private function runner()
  {
    $link_msg = "Visit https://dev-{$this->getSiteName()}.pantheonsite.io/ to view the site.";

    try {
      if (!$this->getSiteInstance()->isFrozen()) {
        $messages = [
          "No need to thaw, {$this->getSiteName()} isn't frozen.",
          "If you don't see the site loading, try again later. It can take up to 15 minutes to thaw.",
          $link_msg,
        ];
        $this->io()->block($messages, 'Note', 'comment');
      } else {
        $messages = [
          sprintf('Thawing %s...', $this->getSiteName()),
          $link_msg,
        ];
        $this->io()->block($messages, 'Note', 'comment');
        $this
          ->thaw()
          ->showFrozenMessage();
      }
    } catch (\Throwable $th) {
      $this->io()->error($th->getMessage());
    }

    return $this;
  }
}
