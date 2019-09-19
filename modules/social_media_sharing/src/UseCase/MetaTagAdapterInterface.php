<?php

namespace Drupal\social_media_sharing\UseCase;

interface MetaTagAdapterInterface {

  /**
   * @param string $key
   *
   * @return string
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function getTagValue(string $key):string;

  /**
   * If not found returns empty string
   *
   * @param string $key
   *
   * @return string
   */
  public function getSafeTagValue(string $key):string;
}