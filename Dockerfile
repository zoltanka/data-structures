FROM webdevops/php-dev:7.4

ARG APP_USER="application"
ARG APP_PATH="/src"

RUN apt-get update -y && apt-get install nano sudo

RUN mkdir -p ${APP_PATH} && chown -R ${APP_USER}:${APP_USER} ${APP_PATH}

RUN adduser ${APP_USER} sudo

#COPY docker/config/ /

WORKDIR ${APP_PATH}

COPY . .

RUN chown -R ${APP_USER}:${APP_USER} ${APP_PATH}

USER ${APP_USER}

RUN rm -fr vendor > /dev/null

RUN composer install --optimize-autoloader --no-suggest --no-interaction --no-scripts

RUN chmod a+x ./vendor/bin/*

#RUN bash